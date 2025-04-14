<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Stock;
use PDF;
use App\Models\CompanyDetails;
use App\Models\SpecialOfferDetails;
use App\Models\FlashSellDetails;
use App\Models\DeliveryMan;
use DataTables;
use App\Models\CancelledOrder;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\Validator;
use App\Models\SupplierStock;
use Illuminate\Support\Facades\Auth;
use App\Models\BuyOneGetOne;
use App\Models\BundleProduct;
use App\Models\PaymentGateway;
use Omnipay\Omnipay;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\CampaignRequestProduct;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\Warehouse;
use App\Models\StockHistory;
use App\Models\ContactEmail;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusChangedMail;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Mail\AdminNotificationMail;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'house_number' => 'required|string|max:255',
            'street_name' => 'required|string|max:255',
            'town' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'note' => 'nullable|string|max:255',
            'payment_method' => 'required',
            'order_summary.*.quantity' => 'required|numeric|min:1',
            'order_summary.*.size' => 'nullable|string|max:255',
            'order_summary.*.color' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Please enter your name.',
            'surname.required' => 'Please enter your last name.',
            'email.required' => 'Please enter your email.',
            'phone.required' => 'Please enter your phone number.',
            'house_number.required' => 'Please enter your house number.',
            'street_name.required' => 'Please enter your street name.',
            'town.required' => 'Please enter your town.',
            'postcode.required' => 'Please enter your postcode.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $formData = $request->all();
        $pdfUrl = null;
        $subtotal = 0.00;
        $discountAmount = 0.00;

        foreach ($formData['order_summary'] as $item) {
            $isBundle = isset($item['bundleId']);
            $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

            if ($isBundle) {
                $bundlePrice = $entity->price ?? 0;
                $totalPrice = (float) $item['quantity'] * $bundlePrice;
            } else {
                if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                    $supplierStock = SupplierStock::where('product_id', $item['productId'])
                        ->where('supplier_id', $item['supplierId'])
                        ->first();

                    if ($supplierStock) {
                        $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                    }
                } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                    $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                        ->first();

                    if ($campaign) {
                        $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                    }
                } elseif (isset($item['bogoId']) && $item['bogoId'] !== null) {
                    $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                        ->first();

                    if ($buyOneGetOne) {
                        $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                    }
                } elseif (isset($item['offerId']) && $item['offerId'] == 1) {
                    $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                        ->where('status', 1)
                        ->first();

                    if ($specialOfferDetail) {
                        $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                    } else {
                        
                        $sellingPrice = Product::find($item['productId'])->stockhistory()
                            ->where('available_qty', '>', 0)
                            ->orderBy('id', 'asc')
                            ->value('selling_price');

                        $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                    }
                } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                    $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                        ->where('status', 1)
                        ->first();

                    if ($flashSellDetail) {
                        $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                    } else {
                        $sellingPrice = Product::find($item['productId'])->stockhistory()
                            ->where('available_qty', '>', 0)
                            ->orderBy('id', 'asc')
                            ->value('selling_price');
                        $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                    }
                } else {
                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                        ->where('available_qty', '>', 0)
                        ->orderBy('id', 'asc')
                        ->value('selling_price');
                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                }
            }

            $subtotal += $totalPrice;
        }

        $discountPercentage = (float)($formData['discount_percentage'] ?? 0.00);
        $discountAmount = (float)($formData['discount_amount'] ?? 0.00);

        if ($discountPercentage > 0) {
            $discountAmount = ($subtotal * $discountPercentage) / 100;
        }

        $vat_percent = 5;
        $vat_amount = ($subtotal * $vat_percent) / 100;
        $shippingAmount = $formData['shipping'] ?? 0;
        $netAmount = $subtotal - $discountAmount + $vat_amount + $shippingAmount;

        if ($formData['payment_method'] === 'paypal') {
            return $this->initiatePayPalPayment($netAmount, $formData);
        }elseif ($formData['payment_method'] === 'stripe') {
            return $this->initiateStripePayment($netAmount, $formData);
        }else {
            DB::transaction(function () use ($formData, &$pdfUrl) {
                $subtotal = 0.00;
    
                $order = new Order();
                if (auth()->check()) {
                    $order->user_id = auth()->user()->id;
                }
                $order->invoice = random_int(100000, 999999);
                $order->purchase_date = date('Y-m-d');
                $order->name = $formData['name'] ?? null;
                $order->surname = $formData['surname'] ?? null;
                $order->email = $formData['email'] ?? null;
                $order->phone = $formData['phone'] ?? null;
                $order->house_number = $formData['house_number'] ?? null;
                $order->street_name = $formData['street_name'] ?? null;
                $order->town = $formData['town'] ?? null;
                $order->postcode = $formData['postcode'] ?? null;
                $order->note = $formData['note'] ?? null;
                $order->payment_method = $formData['payment_method'] ?? null;
                $order->shipping_amount = $formData['shipping'] ?? 0;
                $order->status = 1;
                $order->admin_notify = 1;
                $order->order_type = 0;
    
                foreach ($formData['order_summary'] as $item) {
                    $isBundle = isset($item['bundleId']);
                    $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);
    
                    if ($isBundle) {
                        $bundlePrice = $entity->price ?? 0;
                        $totalPrice = (float) $item['quantity'] * $bundlePrice;
                        $order->bundle_product_id = $entity->id;
                        $entity->quantity -= $item['quantity'];
                        $entity->save();
                    } else {
                        if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                            $supplierStock = SupplierStock::where('product_id', $item['productId'])
                                ->where('supplier_id', $item['supplierId'])
                                ->first();
    
                            if ($supplierStock) {
                                $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                                $supplierStock->quantity -= $item['quantity'];
                                $supplierStock->save();
                            }
                        } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                            $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                                ->first();
    
                            if ($campaign) {
                                $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                                $campaign->quantity -= $item['quantity'];
                                $campaign->save();
                            }
                        } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                            $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                                ->first();
    
                            if ($buyOneGetOne) {
                                $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                                $buyOneGetOne->quantity -= $item['quantity'];
                                $buyOneGetOne->save();
                            }
                        } else {
                            if (isset($item['offerId']) && $item['offerId'] == 1) {
                                $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();
    
                                if ($specialOfferDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                                } else {
                                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                }
                            } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                                $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();
    
                                if ($flashSellDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                                } else {
                                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
    
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                }
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stockhistory()
                                    ->where('available_qty', '>', 0)
                                    ->orderBy('id', 'asc')
                                    ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                        }
                    }
    
                    $subtotal += $totalPrice;
                }
    
                $discountPercentage = $formData['discount_percentage'] ?? null;
                $discountAmount = $formData['discount_amount'] ?? null;
    
                if ($discountPercentage !== null) {
                    $discountPercent = (float) $discountPercentage;
                    $discountAmount = ($subtotal * $discountPercent) / 100;
                } elseif ($discountAmount === null) {
                    $discountAmount = 0.00;
                }
                
                $order->discount_amount = $discountAmount;
                $order->subtotal_amount = $subtotal;
                $order->vat_percent = 5;
                $order->vat_amount = ($subtotal * 5) / 100;
                $order->net_amount = $subtotal + $order->vat_amount + $order->shipping_amount - $discountAmount;
    
                if (auth()->check()) { 
                    $order->created_by = auth()->user()->id;
                }
    
                $order->save();
    
                $encoded_order_id = base64_encode($order->id);
                $pdfUrl = route('generate-pdf', ['encoded_order_id' => $encoded_order_id]);

                $this->sendOrderEmail($order, $pdfUrl);

                if ($discountAmount > 0 && isset($formData['coupon_id'])) {
                    $couponUsage = new CouponUsage();
                    $couponUsage->coupon_id = $formData['coupon_id'];
                    $couponUsage->order_id = $order->id;
                
                    if (auth()->check()) {
                        $couponUsage->user_id = auth()->user()->id;
                    } else {
                        $couponUsage->guest_name = $formData['name'] ?? null;
                        $couponUsage->guest_email = $formData['email'] ?? null;
                        $couponUsage->guest_phone = $formData['phone'] ?? null;
                    }
                
                    $couponUsage->save();
    
                    Coupon::where('id', $formData['coupon_id'])->increment('times_used', 1);
                }
    
                if (isset($formData['order_summary']) && is_array($formData['order_summary'])) {
                    foreach ($formData['order_summary'] as $item) {
                        $isBundle = isset($item['bundleId']);
                        $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);
    
                        $totalPrice = 0;
                        $orderDetail = new OrderDetails();
                        $orderDetail->order_id = $order->id;
                        $orderDetail->product_id = $isBundle ? null : $item['productId'];
                        $orderDetail->quantity = $item['quantity'];
                        $orderDetail->size = $item['size'] ?? null;
                        $orderDetail->color = $item['color'] ?? null;
    
                        if ($isBundle) {
                            $bundlePrice = $entity->price ?? 0;
                            $totalPrice = (float) $item['quantity'] * $bundlePrice;
                            $orderDetail->price_per_unit = $bundlePrice;
                            $orderDetail->total_price = $totalPrice;
                            $orderDetail->bundle_product_ids = $entity->product_ids;
                        } else {
                            if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                                $supplierStock = SupplierStock::where('product_id', $item['productId'])
                                    ->where('supplier_id', $item['supplierId'])
                                    ->first();
    
                                if ($supplierStock) {
                                    $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                                    // $supplierStock->quantity -= $item['quantity'];
                                    $supplierStock->save();
                                }
                                $orderDetail->supplier_id = $item['supplierId'];
    
                            } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                                $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                                    ->first();
    
                                if ($campaign) {
                                    $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                                    // $campaign->quantity -= $item['quantity'];
                                    $campaign->save();
                                }
                                $orderDetail->campaign_request_product_id = $item['campaignId'];
                            } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                                $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                                    ->first();
    
                                if ($buyOneGetOne) {
                                    $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                                    $buyOneGetOne->quantity -= $item['quantity'];
                                    $buyOneGetOne->save();
                                }
                                $orderDetail->buy_one_get_ones_id  = $item['bogoId'];
    
                            } else {
                                if (isset($item['offerId']) && $item['offerId'] == 1) {
                                    $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                        ->where('status', 1)
                                        ->first();
    
                                    if ($specialOfferDetail) {
                                        $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                                    } else {
                                        $sellingPrice = Product::find($item['productId'])->stockhistory()
                                            ->where('available_qty', '>', 0)
                                            ->orderBy('id', 'asc')
                                            ->value('selling_price');
                                        $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                    }
                                    $orderDetail->supplier_id = $item['supplierId'];
    
                                } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                                    $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                        ->where('status', 1)
                                        ->first();
    
                                    if ($flashSellDetail) {
                                        $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                                    } else {
                                        $sellingPrice = Product::find($item['productId'])->stockhistory()
                                            ->where('available_qty', '>', 0)
                                            ->orderBy('id', 'asc')
                                            ->value('selling_price');
                                        $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                    }
                                } else {
                                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                }
                                // if ($entity->stock) {
                                //     $entity->stock->quantity -= $item['quantity'];
                                //     $entity->stock->save();
                                // }
                            }
                            $orderDetail->price_per_unit = $totalPrice / $item['quantity'];
                            $orderDetail->total_price = $totalPrice;
                        }
    
                        $orderDetail->save();
                    }
                }
            });
        }

        return response()->json([
            'success' => true,
            'redirectUrl' => route('order.success', ['pdfUrl' => $pdfUrl])
        ]);
    }

    private function initiateStripePayment($netAmount, $formData)
    {
        $totalamt = $netAmount;
        // $stripecommission = $totalamt * 1.5 / 100;
        // $fixedFee = 0.20;
        // $amt = $netAmount;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $totalamt * 100,
                'currency' => 'GBP',
                'payment_method' =>  $formData['payment_method_id'],
                'description' => 'Order payment',
                'confirm' => false,
                'confirmation_method' => 'automatic',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        $pdfUrl = null;

        DB::transaction(function () use ($formData, &$pdfUrl) {
            $subtotal = 0.00;

            $order = new Order();
            if (auth()->check()) {
                $order->user_id = auth()->user()->id;
            }
            $order->invoice = random_int(100000, 999999);
            $order->purchase_date = date('Y-m-d');
            $order->name = $formData['name'] ?? null;
            $order->surname = $formData['surname'] ?? null;
            $order->email = $formData['email'] ?? null;
            $order->phone = $formData['phone'] ?? null;
            $order->house_number = $formData['house_number'] ?? null;
            $order->street_name = $formData['street_name'] ?? null;
            $order->town = $formData['town'] ?? null;
            $order->postcode = $formData['postcode'] ?? null;
            $order->note = $formData['note'] ?? null;
            $order->payment_method = $formData['payment_method'] ?? null;
            $order->shipping_amount = $formData['shipping'] ?? 0;
            $order->status = 1;
            $order->admin_notify = 1;
            $order->order_type = 0;

            foreach ($formData['order_summary'] as $item) {
                $isBundle = isset($item['bundleId']);
                $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                if ($isBundle) {
                    $bundlePrice = $entity->price ?? 0;
                    $totalPrice = (float) $item['quantity'] * $bundlePrice;
                    $order->bundle_product_id = $entity->id;
                    $entity->quantity -= $item['quantity'];
                    $entity->save();
                } else {
                    if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                        $supplierStock = SupplierStock::where('product_id', $item['productId'])
                            ->where('supplier_id', $item['supplierId'])
                            ->first();

                        if ($supplierStock) {
                            $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                            $supplierStock->quantity -= $item['quantity'];
                            $supplierStock->save();
                        }
                    } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                        $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                            ->first();

                        if ($campaign) {
                            $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                            $campaign->quantity -= $item['quantity'];
                            $campaign->save();
                        }
                    } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                        $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                            ->first();

                        if ($buyOneGetOne) {
                            $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                            $buyOneGetOne->quantity -= $item['quantity'];
                            $buyOneGetOne->save();
                        }
                    } else {
                        if (isset($item['offerId']) && $item['offerId'] == 1) {
                            $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($specialOfferDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                        } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                            $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($flashSellDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                        } else {
                            $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                        }
                    }
                }

                $subtotal += $totalPrice;
            }

            $discountPercentage = $formData['discount_percentage'] ?? null;
            $discountAmount = $formData['discount_amount'] ?? null;

            if ($discountPercentage !== null) {
                $discountPercent = (float) $discountPercentage;
                $discountAmount = ($subtotal * $discountPercent) / 100;
            } elseif ($discountAmount === null) {
                $discountAmount = 0.00;
            }
            
            $order->discount_amount = $discountAmount;
            $order->subtotal_amount = $subtotal;
            $order->vat_percent = 5;
            $order->vat_amount = ($subtotal * 5) / 100;
            $order->net_amount = $subtotal + $order->vat_amount + $order->shipping_amount - $discountAmount;

            if (auth()->check()) { 
                $order->created_by = auth()->user()->id;
            }

            $order->save();

            $encoded_order_id = base64_encode($order->id);
            $pdfUrl = route('generate-pdf', ['encoded_order_id' => $encoded_order_id]);

            $this->sendOrderEmail($order, $pdfUrl);

            if ($discountAmount > 0 && isset($formData['coupon_id'])) {
                $couponUsage = new CouponUsage();
                $couponUsage->coupon_id = $formData['coupon_id'];
                $couponUsage->order_id = $order->id;
            
                if (auth()->check()) {
                    $couponUsage->user_id = auth()->user()->id;
                } else {
                    $couponUsage->guest_name = $formData['name'] ?? null;
                    $couponUsage->guest_email = $formData['email'] ?? null;
                    $couponUsage->guest_phone = $formData['phone'] ?? null;
                }
            
                $couponUsage->save();

                Coupon::where('id', $formData['coupon_id'])->increment('times_used', 1);
            }

            if (isset($formData['order_summary']) && is_array($formData['order_summary'])) {
                foreach ($formData['order_summary'] as $item) {
                    $isBundle = isset($item['bundleId']);
                    $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                    $totalPrice = 0;
                    $orderDetail = new OrderDetails();
                    $orderDetail->order_id = $order->id;
                    $orderDetail->product_id = $isBundle ? null : $item['productId'];
                    $orderDetail->quantity = $item['quantity'];
                    $orderDetail->size = $item['size'] ?? null;
                    $orderDetail->color = $item['color'] ?? null;

                    if ($isBundle) {
                        $bundlePrice = $entity->price ?? 0;
                        $totalPrice = (float) $item['quantity'] * $bundlePrice;
                        $orderDetail->price_per_unit = $bundlePrice;
                        $orderDetail->total_price = $totalPrice;
                        $orderDetail->bundle_product_ids = $entity->product_ids;
                    } else {
                        if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                            $supplierStock = SupplierStock::where('product_id', $item['productId'])
                                ->where('supplier_id', $item['supplierId'])
                                ->first();

                            if ($supplierStock) {
                                $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                                // $supplierStock->quantity -= $item['quantity'];
                                $supplierStock->save();
                            }
                            $orderDetail->supplier_id = $item['supplierId'];

                        } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                            $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                                ->first();

                            if ($campaign) {
                                $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                                // $campaign->quantity -= $item['quantity'];
                                $campaign->save();
                            }
                            $orderDetail->campaign_request_product_id = $item['campaignId'];
                        } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                            $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                                ->first();

                            if ($buyOneGetOne) {
                                $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                                $buyOneGetOne->quantity -= $item['quantity'];
                                $buyOneGetOne->save();
                            }
                            $orderDetail->buy_one_get_ones_id  = $item['bogoId'];

                        } else {
                            if (isset($item['offerId']) && $item['offerId'] == 1) {
                                $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();

                                if ($specialOfferDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                                } else {
                                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                }
                                $orderDetail->supplier_id = $item['supplierId'];

                            } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                                $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();

                                if ($flashSellDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                                } else {
                                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                }
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                            // if ($entity->stock) {
                            //     $entity->stock->quantity -= $item['quantity'];
                            //     $entity->stock->save();
                            // }
                        }
                        $orderDetail->price_per_unit = $totalPrice / $item['quantity'];
                        $orderDetail->total_price = $totalPrice;
                    }

                    $orderDetail->save();
                }
            }
        });

        return response()->json([
            'success' => true,
            'client_secret' => $paymentIntent->client_secret,
            'redirectUrl' => route('order.success', ['pdfUrl' => $pdfUrl])
        ]);
    }

    protected function getPayPalCredentials()
    {
        return PaymentGateway::where('name', 'paypal')
            ->where('status', 1)
            ->first();
    }

    protected function initiatePayPalPayment($netAmount, $formData)
    {
        $payPalCredentials = $this->getPayPalCredentials();

        if (!$payPalCredentials) {
            return response()->json(['error' => 'PayPal credentials not found'], 404);
        }

        $gateway = Omnipay::create('PayPal_Rest');
        $gateway->setClientId($payPalCredentials->clientid);
        $gateway->setSecret($payPalCredentials->secretid);
        $gateway->setTestMode($payPalCredentials->mode);

        try {
            $response = $gateway->purchase([
                'amount' => number_format($netAmount, 2, '.', ''),
                'currency' => 'GBP',
                'returnUrl' => route('payment.success'),
                'cancelUrl' => route('payment.cancel')
            ])->send();

            if ($response->isRedirect()) {
                session()->put('order_data', $formData);
                session()->put('order_net_amount', $netAmount);
                return response()->json(['redirectUrl' => $response->getRedirectUrl()]);
            } else {
                return response()->json(['error' => $response->getMessage()], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function paymentSuccess(Request $request)
    {
        $formData = session('order_data');
        DB::transaction(function () use ($formData, &$pdfUrl) {
            $subtotal = 0.00;

            $order = new Order();
            if (auth()->check()) {
                $order->user_id = auth()->user()->id;
            }
            $order->invoice = random_int(100000, 999999);
            $order->purchase_date = date('Y-m-d');
            $order->name = $formData['name'] ?? null;
            $order->surname = $formData['surname'] ?? null;
            $order->email = $formData['email'] ?? null;
            $order->phone = $formData['phone'] ?? null;
            $order->house_number = $formData['house_number'] ?? null;
            $order->street_name = $formData['street_name'] ?? null;
            $order->town = $formData['town'] ?? null;
            $order->postcode = $formData['postcode'] ?? null;
            $order->note = $formData['note'] ?? null;
            $order->payment_method = $formData['payment_method'] ?? null;
            $order->shipping_amount = $formData['shipping'] ?? 0.00;
            $order->status = 1;
            $order->admin_notify = 1;
            $order->order_type = 0;

            if (isset($formData['order_summary']) && is_array($formData['order_summary'])) {
            foreach ($formData['order_summary'] as $item) {
                $isBundle = isset($item['bundleId']);
                $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                if ($isBundle) {
                    $bundlePrice = $entity->price ?? 0;
                    $totalPrice = (float) $item['quantity'] * $bundlePrice;
                    $order->bundle_product_id = $entity->id;
                    $entity->quantity -= $item['quantity'];
                    $entity->save();
                } else {
                    if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                        $supplierStock = SupplierStock::where('product_id', $item['productId'])
                            ->where('supplier_id', $item['supplierId'])
                            ->first();

                        if ($supplierStock) {
                            $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                            $supplierStock->quantity -= $item['quantity'];
                            $supplierStock->save();
                        }
                    } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                        $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                            ->first();

                        if ($campaign) {
                            $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                            $campaign->quantity -= $item['quantity'];
                            $campaign->save();
                        }
                    } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                        $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                            ->first();

                        if ($buyOneGetOne) {
                            $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                            $buyOneGetOne->quantity -= $item['quantity'];
                            $buyOneGetOne->save();
                        }
                    } else {
                        if (isset($item['offerId']) && $item['offerId'] == 1) {
                            $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($specialOfferDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                        } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                            $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                ->where('status', 1)
                                ->first();

                            if ($flashSellDetail) {
                                $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                        } else {
                            $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                            $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                        }
                    }
                }

                $subtotal += $totalPrice;
            }
            }

            $discountPercentage = $formData['discount_percentage'] ?? null;
            $discountAmount = $formData['discount_amount'] ?? null;

            if ($discountPercentage !== null) {
                $discountPercent = (float) $discountPercentage;
                $discountAmount = ($subtotal * $discountPercent) / 100;
            } elseif ($discountAmount === null) {
                $discountAmount = 0.00;
            }
            
            $order->discount_amount = $discountAmount;
            $order->subtotal_amount = $subtotal;
            $order->vat_percent = 5;
            $order->vat_amount = ($subtotal * 5) / 100;
            $order->net_amount = $subtotal + $order->vat_amount + $order->shipping_amount - $discountAmount;

            if (auth()->check()) { 
                $order->created_by = auth()->user()->id;
            }

            $order->save();

            $encoded_order_id = base64_encode($order->id);
            $pdfUrl = route('generate-pdf', ['encoded_order_id' => $encoded_order_id]);

            $this->sendOrderEmail($order, $pdfUrl);

            if ($discountAmount > 0 && isset($formData['coupon_id'])) {
                $couponUsage = new CouponUsage();
                $couponUsage->coupon_id = $formData['coupon_id'];
                $couponUsage->order_id = $order->id;
            
                if (auth()->check()) {
                    $couponUsage->user_id = auth()->user()->id;
                } else {
                    $couponUsage->guest_name = $formData['name'] ?? null;
                    $couponUsage->guest_email = $formData['email'] ?? null;
                    $couponUsage->guest_phone = $formData['phone'] ?? null;
                }
            
                $couponUsage->save();

                Coupon::where('id', $formData['coupon_id'])->increment('times_used', 1);
            }

            if (isset($formData['order_summary']) && is_array($formData['order_summary'])) {
                foreach ($formData['order_summary'] as $item) {
                    $isBundle = isset($item['bundleId']);
                    $entity = $isBundle ? BundleProduct::findOrFail($item['bundleId']) : Product::findOrFail($item['productId']);

                    $totalPrice = 0;
                    $orderDetail = new OrderDetails();
                    $orderDetail->order_id = $order->id;
                    $orderDetail->product_id = $isBundle ? null : $item['productId'];
                    $orderDetail->quantity = $item['quantity'];
                    $orderDetail->size = $item['size'] ?? null;
                    $orderDetail->color = $item['color'] ?? null;

                    if ($isBundle) {
                        $bundlePrice = $entity->price ?? 0;
                        $totalPrice = (float) $item['quantity'] * $bundlePrice;
                        $orderDetail->price_per_unit = $bundlePrice;
                        $orderDetail->total_price = $totalPrice;
                        $orderDetail->bundle_product_ids = $entity->product_ids;
                    } else {
                        if (isset($item['supplierId']) && $item['supplierId'] !== null) {
                            $supplierStock = SupplierStock::where('product_id', $item['productId'])
                                ->where('supplier_id', $item['supplierId'])
                                ->first();

                            if ($supplierStock) {
                                $totalPrice = (float) $item['quantity'] * (float) $supplierStock->price;
                                // $supplierStock->quantity -= $item['quantity'];
                                $supplierStock->save();
                            }
                            $orderDetail->supplier_id = $item['supplierId'];

                        } elseif (isset($item['campaignId']) && $item['campaignId'] !== null) {
                            $campaign = CampaignRequestProduct::where('product_id', $item['productId'])
                                ->first();

                            if ($campaign) {
                                $totalPrice = (float) $item['quantity'] * (float) $campaign->campaign_price;
                                // $campaign->quantity -= $item['quantity'];
                                $campaign->save();
                            }
                            $orderDetail->campaign_request_product_id = $item['campaignId'];
                        } else if (isset($item['bogoId']) && $item['bogoId'] !== null) {
                            $buyOneGetOne = BuyOneGetOne::where('product_id', $item['productId'])
                                ->first();

                            if ($buyOneGetOne) {
                                $totalPrice = (float) $item['quantity'] * (float) $buyOneGetOne->price;
                                $buyOneGetOne->quantity -= $item['quantity'];
                                $buyOneGetOne->save();
                            }
                            $orderDetail->buy_one_get_ones_id  = $item['bogoId'];

                        } else {
                            if (isset($item['offerId']) && $item['offerId'] == 1) {
                                $specialOfferDetail = SpecialOfferDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();

                                if ($specialOfferDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $specialOfferDetail->offer_price;
                                } else {
                                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                }
                                $orderDetail->supplier_id = $item['supplierId'];

                            } elseif (isset($item['offerId']) && $item['offerId'] == 2) {
                                $flashSellDetail = FlashSellDetails::where('product_id', $item['productId'])
                                    ->where('status', 1)
                                    ->first();

                                if ($flashSellDetail) {
                                    $totalPrice = (float) $item['quantity'] * (float) $flashSellDetail->flash_sell_price;
                                } else {
                                    $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                    $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                                }
                            } else {
                                $sellingPrice = Product::find($item['productId'])->stockhistory()
                                        ->where('available_qty', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->value('selling_price');
                                $totalPrice = (float) $item['quantity'] * (float) ($sellingPrice ?? $entity->price);
                            }
                            // if ($entity->stock) {
                            //     $entity->stock->quantity -= $item['quantity'];
                            //     $entity->stock->save();
                            // }
                        }
                        $orderDetail->price_per_unit = $totalPrice / $item['quantity'];
                        $orderDetail->total_price = $totalPrice;
                    }

                    $orderDetail->save();
                }
            }
        });

        session()->forget('order_data');

        // return redirect($pdfUrl);
        return view('frontend.order.success', compact('pdfUrl'));
    }

    protected function sendOrderEmail($order, $pdfUrl)
    {
        Mail::to($order->email)->send(new OrderConfirmation($order, $pdfUrl));

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');
        foreach ($contactEmails as $email) {
            Mail::to($email)->send(new OrderConfirmation($order, $pdfUrl));
        }
    }

    public function paymentCancel()
    {
        return view('frontend.order.cancel');
    }

    public function orderSuccess(Request $request)
    {
        $pdfUrl = $request->input('pdfUrl');
        return view('frontend.order.success', compact('pdfUrl'));
    }

    public function generatePDF($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $order = Order::with('orderDetails')->findOrFail($order_id);

        $data = [
            'order' => $order,
            'currency' => CompanyDetails::value('currency'),
            'bundleProduct' => $order->bundle_product_id ? BundleProduct::find($order->bundle_product_id) : null,
        ];

        $pdf = PDF::loadView('frontend.order_pdf', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }

    public function generatePDFForSupplier($encoded_order_id)
    {
        $order_id = base64_decode($encoded_order_id);
        $supplierId = Auth::guard('supplier')->user()->id;

        $orderDetails = OrderDetails::where('order_id', $order_id)
            ->where('supplier_id', $supplierId)
            ->with(['product', 'order.user'])
            ->get();

        $order = $orderDetails->first()->order ?? null;
        
        if (!$order) {
            abort(404, 'Order not found for the supplier.');
        }

        $data = [
            'order' => $order,
            'orderDetails' => $orderDetails,
            'currency' => CompanyDetails::value('currency'),
        ];

        $pdf = PDF::loadView('supplier.order_pdf_supplier', $data);

        return $pdf->stream('order_' . $order->id . '.pdf');
    }

    public function getOrders()
    {
        $orders = Order::where('user_id', auth()->user()->id)
                ->orderBy('id', 'desc')
                ->get();
        return view('user.orders', compact('orders'));
    }

    public function getAllOrder(Request $request, $userId = null)
    {

        if (!(in_array('20', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if ($request->ajax()) {
            $userId = $request->get('userId') ?? $userId;

            if ($userId) {
                $ordersQuery = Order::with('user')->where('user_id', $userId)
                    ->whereIn('order_type', [0, 1]);
            } else {
                $ordersQuery = Order::with('user')->whereIn('order_type', [0, 1]);
            }
            
            $ordersQuery->where('status', '!=', 7);

            return DataTables::of($ordersQuery->orderBy('id', 'desc'))
                ->addColumn('action', function($order) {
                    $invoiceButton = '';
                    if ($order->order_type === 0) {
                        $invoiceButton = '<a href="' . route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    } elseif ($order->order_type === 1) {
                        $invoiceButton = '<a href="' . route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    }
                
                    $detailsButton = '<a href="' . route('admin.orders.details', ['orderId' => $order->id]) . '" class="btn btn-info btn-round btn-shadow">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    <a href="' . route('order-edit', ['orderId' => $order->id]) . '" class="btn btn-warning btn-round btn-shadow">
                                        <i class="fas fa-edit"></i> Edit
                                    </a> ';
                
                    return $invoiceButton . ' ' . $detailsButton;
                })            
                ->editColumn('subtotal_amount', function ($order) {
                    return number_format($order->subtotal_amount, 2);
                })
                ->editColumn('paid_amount', function ($order) {
                    return number_format($order->paid_amount, 2);
                })
                ->editColumn('due_amount', function ($order) {
                    return number_format($order->due_amount, 2);
                })
                ->editColumn('discount_amount', function ($order) {
                    return number_format($order->discount_amount, 2);
                })
                ->editColumn('net_amount', function ($order) {
                    return number_format($order->net_amount, 2);
                })
                ->editColumn('payment_method', function ($order) {
                    if ($order->payment_method === 'cashOnDelivery') {
                        return 'Cash On Delivery';
                    } elseif ($order->payment_method === 'paypal') {
                        return 'PayPal';
                    } elseif ($order->payment_method === 'stripe') {
                        return 'Stripe';
                    } else {
                        return $order->payment_method;
                    }
                })
                ->editColumn('status', function ($order) {
                    $statusLabels = [
                        1 => 'Pending',
                        2 => 'Processing',
                        3 => 'Packed',
                        4 => 'Shipped',
                        5 => 'Delivered',
                        6 => 'Returned',
                        7 => 'Cancelled'
                    ];
                    return isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : 'Unknown';
                })
                ->addColumn('purchase_date', function ($order) {
                    return Carbon::parse($order->purchase_date)->format('d-m-Y');
                })
                ->addColumn('name', function ($order) {
                    if ($order->user) {
                        return ($order->user->name ?? '') . '<br>' . 
                               ($order->user->email ?? '') . '<br>' . 
                               ($order->user->phone ?? '');
                    } else {
                        return ($order->name ?? '') . '<br>' . 
                               ($order->email ?? '') . '<br>' . 
                               ($order->phone ?? '');
                    }
                })
                ->addColumn('type', function ($order) {
                    return $order->order_type == 0 ? 'Frontend' : 'In-house Sale';
                })
                ->rawColumns(['action','name'])
                ->make(true);
        }
        return view('admin.orders.all', compact('userId'));
    }

    public function getInHouseOrder(Request $request, $userId = null)
    {

        if (!(in_array('25', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        if ($request->ajax()) {
            $userId = $request->get('userId') ?? $userId;

            if ($userId) {
                $ordersQuery = Order::with('user')->where('user_id', $userId)
                    ->whereIn('order_type', [1]);
            } else {
                $ordersQuery = Order::with('user')->whereIn('order_type', [1]);
            }
            
            $ordersQuery->where('status', '!=', 7);

            return DataTables::of($ordersQuery->orderBy('id', 'desc'))
                ->addColumn('action', function($order) {
                    $invoiceButton = '';
                    if ($order->order_type === 0) {
                        $invoiceButton = '<a href="' . route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    } elseif ($order->order_type === 1) {
                        $invoiceButton = '<a href="' . route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    }
                
                    $detailsButton = '<a href="' . route('admin.orders.details', ['orderId' => $order->id]) . '" class="btn btn-info btn-round btn-shadow mt-1">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    <a href="' . route('order-edit', ['orderId' => $order->id]) . '" class="btn btn-warning btn-round btn-shadow mt-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a> ';
                
                    return $invoiceButton . ' ' . $detailsButton;
                })            
                ->editColumn('subtotal_amount', function ($order) {
                    return number_format($order->subtotal_amount, 2);
                })
                ->editColumn('paid_amount', function ($order) {
                    return number_format($order->paid_amount, 2);
                })
                ->editColumn('due_amount', function ($order) {
                    return number_format($order->due_amount, 2);
                })
                ->editColumn('discount_amount', function ($order) {
                    return number_format($order->discount_amount, 2);
                })
                ->editColumn('net_amount', function ($order) {
                    return number_format($order->net_amount, 2);
                })
                ->editColumn('status', function ($order) {
                    $statusLabels = [
                        1 => 'Pending',
                        2 => 'Processing',
                        3 => 'Packed',
                        4 => 'Shipped',
                        5 => 'Delivered',
                        6 => 'Returned',
                        7 => 'Cancelled'
                    ];
                    return isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : 'Unknown';
                })
                ->addColumn('purchase_date', function ($order) {
                    return Carbon::parse($order->purchase_date)->format('d-m-Y');
                })
                ->addColumn('name', function ($order) {
                    if ($order->user) {
                        return ($order->user->name ?? '') . '<br>' . 
                               ($order->user->email ?? '') . '<br>' . 
                               ($order->user->phone ?? '');
                    } else {
                        return ($order->name ?? '') . '<br>' . 
                               ($order->email ?? '') . '<br>' . 
                               ($order->phone ?? '');
                    }
                })
                ->addColumn('type', function ($order) {
                    return $order->order_type == 0 ? 'Frontend' : 'In-house Sale';
                })
                ->rawColumns(['action','name'])
                ->make(true);
        }
        return view('admin.orders.inhouse', compact('userId'));
    }

    public function getAllOrderByCoupon(Request $request, $couponId)
    {
        if ($request->ajax()) {
            $couponUsages = CouponUsage::where('coupon_id', $couponId)
            ->pluck('order_id'); 
            
            $ordersQuery = Order::with('user')->whereIn('id', $couponUsages)->whereIn('order_type', [0, 1])->where('status', '!=', 7);

            return DataTables::of($ordersQuery->orderBy('id', 'desc'))
                ->addColumn('action', function($order) {
                    $invoiceButton = '';
                    if ($order->order_type === 0) {
                        $invoiceButton = '<a href="' . route('generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    } elseif ($order->order_type === 1) {
                        $invoiceButton = '<a href="' . route('in-house-sell.generate-pdf', ['encoded_order_id' => base64_encode($order->id)]) . '" class="btn btn-success btn-round btn-shadow" target="_blank">
                                            <i class="fas fa-receipt"></i> Invoice
                                        </a>';
                    }
                
                    $detailsButton = '<a href="' . route('admin.orders.details', ['orderId' => $order->id]) . '" class="btn btn-info btn-round btn-shadow">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    <a href="' . route('order-edit', ['orderId' => $order->id]) . '" class="btn btn-warning btn-round btn-shadow">
                                        <i class="fas fa-edit"></i> Edit
                                    </a> ';
                
                    return $invoiceButton . ' ' . $detailsButton;
                })            
                ->editColumn('subtotal_amount', function ($order) {
                    return number_format($order->subtotal_amount, 2);
                })
                ->editColumn('paid_amount', function ($order) {
                    return number_format($order->paid_amount, 2);
                })
                ->editColumn('due_amount', function ($order) {
                    return number_format($order->due_amount, 2);
                })
                ->editColumn('discount_amount', function ($order) {
                    return number_format($order->discount_amount, 2);
                })
                ->editColumn('net_amount', function ($order) {
                    return number_format($order->net_amount, 2);
                })
                ->editColumn('status', function ($order) {
                    $statusLabels = [
                        1 => 'Pending',
                        2 => 'Processing',
                        3 => 'Packed',
                        4 => 'Shipped',
                        5 => 'Delivered',
                        6 => 'Returned',
                        7 => 'Cancelled'
                    ];
                    return isset($statusLabels[$order->status]) ? $statusLabels[$order->status] : 'Unknown';
                })
                ->addColumn('purchase_date', function ($order) {
                    return Carbon::parse($order->purchase_date)->format('d-m-Y');
                })
                ->addColumn('name', function ($order) {
                    if ($order->user) {
                        return ($order->user->name ?? '') . '<br>' . 
                               ($order->user->email ?? '') . '<br>' . 
                               ($order->user->phone ?? '');
                    } else {
                        return ($order->name ?? '') . '<br>' . 
                               ($order->email ?? '') . '<br>' . 
                               ($order->phone ?? '');
                    }
                })
                ->addColumn('type', function ($order) {
                    return $order->order_type == 0 ? 'Frontend' : 'In-house Sale';
                })
                ->rawColumns(['action','name'])
                ->make(true);
        }
        return view('admin.orders.coupon', compact('couponId'));
    }

    public function pendingOrders()
    {
        $orders = Order::with('user','warehouse')
                ->whereIn('order_type', [0, 1])
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->get();
        $warehouses = Warehouse::select('id', 'name','location')->where('status', 1)->get();   
        return view('admin.orders.index', compact('orders', 'warehouses'));
    }

    public function processingOrders()
    {

        if (!(in_array('21', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $orders = Order::with('user')
                ->whereIn('order_type', [0, 1])
                ->where('status', 2)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.index', compact('orders'));
    }
    public function packedOrders()
    {
        $orders = Order::with('user')
                ->whereIn('order_type', [0, 1])
                ->where('status', 3)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.index', compact('orders'));
    }
    public function shippedOrders()
    {
        $orders = Order::with('user')
                ->whereIn('order_type', [0, 1])
                ->where('status', 4)
                ->orderBy('id', 'desc')
                ->get();

        return view('admin.orders.index', compact('orders'));
    }
    public function deliveredOrders()
    {

        if (!(in_array('22', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $orders = Order::with('user')
                ->where('status', 5)
                ->whereIn('order_type', [0, 1])
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.index', compact('orders'));
    }
    public function returnedOrders()
    {
        $orders = Order::with(['user', 'orderReturns.product'])
                    ->where('status', 6)
                    ->whereIn('order_type', [0, 1])
                    ->orderBy('id', 'desc')
                    ->get();

        return view('admin.orders.returned', compact('orders'));
    }
    public function cancelledOrders()
    {

        if (!(in_array('23', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        $orders = Order::with('user', 'cancelledOrder')
                ->whereIn('order_type', [0, 1])
                ->where('status', 7)
                ->orderBy('id', 'desc')
                ->get();
        return view('admin.orders.cancelled', compact('orders'));
    }

    public function updateStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($order) {
            $order->status = $request->status;
            $order->save();

            if ($request->status == 6) {
                $transaction = new Transaction();
                $transaction->date = now();
                $transaction->customer_id = $order->user_id;
                $transaction->order_id = $order->id;
                $transaction->table_type = 'Sales';
                $transaction->amount = $order->net_amount;
                $transaction->at_amount = $order->net_amount;
                $transaction->transaction_type = 'Return';
                $transaction->created_by = auth()->user()->id;
                $transaction->created_ip = request()->ip();
                $transaction->save();
            }

            $emailToSend = $order->email ?? $order->user->email;

            if ($emailToSend) {
                Mail::to($emailToSend)->send(new OrderStatusChangedMail($order));
            }

            $contactEmails = ContactEmail::where('status', 1)->pluck('email');

            foreach ($contactEmails as $email) {
                Mail::to($email)->send(new OrderStatusChangedMail($order));
            }


            return response()->json(['success' => true, 'message' => 'Order status updated successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
    }

    public function updateDeliveryMan(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_man_id' => 'required|exists:delivery_men,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $deliveryMan = DeliveryMan::findOrFail($request->delivery_man_id);
        $order->delivery_man_id = $deliveryMan->id;
        $order->save();
        return response()->json(['success' => true], 200);
    }

    public function showOrder($orderId)
    {
        $order = Order::with(['user', 'orderDetails.product', 'orderDetails.buyOneGetOne', 'bundleProduct'])
            ->where('id', $orderId)
            ->firstOrFail();
            // dd($order);
        return view('admin.orders.details', compact('order'));
    }

    public function markAsNotified(Request $request)
    {
        $order = Order::find($request->order_id);

        if ($order) {
            $order->admin_notify = 0;
            $order->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function showOrderUser($orderId)
    {
        $order = Order::with(['user', 'orderDetails.product'])
            ->where('id', $orderId)
            ->firstOrFail();
        return view('user.order_details', compact('order'));
    }

    public function cancel(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if (in_array($order->status, [4, 5, 6, 7])) {
            return response()->json(['error' => 'Order cannot be cancelled.'], 400);
        }

        $order->status = 7;
        $order->save();

        $orderDetails = OrderDetails::where('order_id', $order->id)->get();

        foreach ($orderDetails as $detail) {
            $stock = Stock::where('product_id', $detail->product_id)
                        ->where('color', $detail->color)
                        ->first();

            // if ($stock) {
            //     $stock->quantity += $detail->quantity;
            //     $stock->save();
            // }
        }

        CancelledOrder::create([
            'order_id' => $order->id,
            'reason' => $request->input('reason'),
            'cancelled_by' => auth()->id(),
        ]);

        return response()->json(['success' => true]);
    }

    public function getOrderDetailsModal(Request $request)
    {
        $orderId = $request->get('order_id');
        $order = Order::with('orderDetails.product')->findOrFail($orderId);
        
        return response()->json([
            'order' => $order,
            'orderDetails' => $order->orderDetails
        ]);
    }

    public function returnStore(Request $request)
    {
        $data = $request->all();

        $order_id = $data['order_id'];

        $order = Order::find($order_id);
        $order->status = 6;
        $order->save();

        $return_items = $data['return_items'];

        foreach ($return_items as $item) {
            $orderReturn = new OrderReturn();
            $orderReturn->product_id = $item['product_id'];
            $orderReturn->order_id = $order_id;
            $orderReturn->quantity = $item['return_quantity'];
            $orderReturn->new_quantity = $item['return_quantity'];
            $orderReturn->reason = $item['return_reason'] ?? '';
            $orderReturn->returned_by = auth()->user()->id;
            $orderReturn->save();
        }

        return response()->json(['message' => 'Order return submitted successfully'], 200);
    }

    public function assignWarehouse(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->warehouse_id = $request->warehouse_id;
        $order->save();
    
        $orderDetails = $order->orderDetails;
    
        foreach ($orderDetails as $orderDetail) {
            $stock = Stock::where('product_id', $orderDetail->product_id)
                ->where('size', $orderDetail->size)
                ->where('color', $orderDetail->color)
                ->where('warehouse_id', $request->warehouse_id)
                ->first();
    
            if ($stock) {
                $stock->quantity -= $orderDetail->quantity;
                $stock->save();
            } else {
                $stock = new Stock();
                $stock->warehouse_id = $request->warehouse_id;
                $stock->product_id = $orderDetail->product_id;
                $stock->size = $orderDetail->size;
                $stock->color = $orderDetail->color;
                $stock->quantity = -$orderDetail->quantity;
                $stock->created_by = auth()->user()->id; 
                $stock->save();
            }
    
            $stockHistories = StockHistory::where('stock_id', $stock->id)
                ->where('available_qty', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();
    
            $requiredQty = $orderDetail->quantity;
    
            foreach ($stockHistories as $stockHistory) {
                if ($requiredQty > 0) {
                    if ($stockHistory->available_qty >= $requiredQty) {
                        $stockHistory->available_qty -= $requiredQty;
                        $stockHistory->selling_qty += $requiredQty;
                        $stockHistory->save();
                        $requiredQty = 0; 
                    } else {
                        $requiredQty -= $stockHistory->available_qty;
                        $stockHistory->selling_qty += $stockHistory->available_qty;
                        $stockHistory->available_qty = 0;
                        $stockHistory->save();
                    }
                } else {
                    break;
                }
            }
        }
    
        return response()->json(['message' => 'Warehouse assigned and stock updated successfully!']);
    }
    
    public function sendMailToAdmin(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'orderId' => 'required|exists:orders,id',
        ]);

        $message = $validated['message'];
        $orderId = $validated['orderId'];

        $order = Order::findOrFail($orderId);

        $contactEmail = ContactEmail::orderBy('id', 'asc')->first();

        if (!$contactEmail) {
            return response()->json(['success' => false, 'message' => 'No contact email found.']);
        }

        Mail::to($contactEmail->email)->send(new AdminNotificationMail($message, $order));
        return response()->json(['success' => true, 'message' => 'Mail sent successfully!']);

    }

    public function orderDueList($userId)
    {
        $orders = Order::where('user_id', $userId)
                    ->where('due_amount', '>', 0)
                    ->where('status', '!=', 7)
                    ->latest()
                    ->get();

        return view('admin.orders.due_orders', compact('orders'));
    }

}
