<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
           {{-- dashboard  --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ (request()->is('admin/dashboard*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <a href="{{ route('toggle.sidebar') }}" class="btn btn-info my-2 d-none">
            Switch to Accounting <i class="fas fa-arrow-right"></i>
        </a>

        @php
            $permissions = [2, 3, 5, 6, 30];
            $userPermissions = json_decode(auth()->user()->role->permission, true);
        @endphp
        <!-- Inventory -->
        @if(array_intersect($permissions, $userPermissions))
        <li class="nav-item dropdown {{ (request()->is('admin/category*') || request()->is('admin/brand*') || request()->is('admin/model*') || request()->is('admin/unit*') || request()->is('admin/group*') || request()->is('admin/size*') || request()->is('admin/color*') || request()->is('admin/product*') || request()->is('admin/sub-category*') || request()->is('admin/create-product*') || request()->is('admin/bogo-product*')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/category*') || request()->is('admin/brand*') || request()->is('admin/model*') || request()->is('admin/unit*') || request()->is('admin/group*') || request()->is('admin/size*') || request()->is('admin/color*') || request()->is('admin/product*') || request()->is('admin/sub-category*') || request()->is('admin/create-product*') || request()->is('admin/bogo-product*')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                    Head of Products<i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @if(in_array('2', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('createproduct') }}" class="nav-link {{ (request()->is('admin/create-product')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Create Product</p>
                    </a>
                </li>
                @endif

                @if(in_array('3', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allproduct') }}" class="nav-link {{ (request()->is('admin/product') || request()->routeIs('productHistory') ) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Manage Products</p>
                    </a>
                </li>
                @endif

                @if(in_array('5', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allcategory') }}" class="nav-link {{ (request()->is('admin/category*')) ? 'active' : '' }}">
                        <i class="far fa-list-alt nav-icon"></i>
                        <p>Categories</p>
                    </a>
                </li>
                @endif

                <!-- <li class="nav-item">
                    <a href="{{ route('allsubcategory') }}" class="nav-link {{ (request()->is('admin/sub-category*')) ? 'active' : '' }}">
                        <i class="far fa-folder nav-icon"></i>
                        <p>Sub Categories</p>
                    </a>
                </li> -->
                <!-- <li class="nav-item">
                    <a href="{{ route('allbrand') }}" class="nav-link {{ (request()->is('admin/brand*')) ? 'active' : '' }}">
                        <i class="fas fa-tags nav-icon"></i>
                        <p>Brands</p>
                    </a>
                </li> -->
                <!-- <li class="nav-item">
                    <a href="{{ route('allmodel') }}" class="nav-link {{ (request()->is('admin/model*')) ? 'active' : '' }}">
                        <i class="fas fa-cogs nav-icon"></i>
                        <p>Models</p>
                    </a>
                </li> -->

                @if(in_array('30', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allunit') }}" class="nav-link {{ (request()->is('admin/unit*')) ? 'active' : '' }}">
                        <i class="fas fa-ruler nav-icon"></i>
                        <p>Units</p>
                    </a>
                </li>
                @endif

                @if(in_array('6', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allgroup') }}" class="nav-link {{ (request()->is('admin/group*')) ? 'active' : '' }}">
                        <i class="fas fa-object-group nav-icon"></i>
                        <p>Groups</p>
                    </a>
                </li>
                @endif

                <!-- <li class="nav-item">
                    <a href="{{ route('allsize') }}" class="nav-link {{ (request()->is('admin/size*')) ? 'active' : '' }}">
                        <i class="fas fa-expand-arrows-alt nav-icon"></i>
                        <p>Size</p>
                    </a>
                </li> -->
                <!-- <li class="nav-item">
                    <a href="{{ route('allcolor') }}" class="nav-link {{ (request()->is('admin/color*')) ? 'active' : '' }}">
                        <i class="fas fa-paint-brush nav-icon"></i>
                        <p>Color</p>
                    </a>
                </li> -->
            </ul>
        </li>
        @endif

        @php
            $permissions = [7, 8, 10, 11, 31, 13, 14, 15];
            $userPermissions = json_decode(auth()->user()->role->permission, true);
        @endphp

        <!-- purchase -->
        @if(array_intersect($permissions, $userPermissions))
        <li class="nav-item dropdown {{( request()->is('admin/purchase-history*') || request()->is('admin/add-stock*') || request()->is('admin/purchase-return-history*') || request()->routeIs('purchase.edit') || request()->routeIs('stockReturnHistory') || request()->routeIs('returnProduct') || request()->routeIs('admin.mothervassel') || request()->routeIs('admin.lightervassel') || request()->routeIs('admin.ghat') || request()->routeIs('allsupplier') || request()->routeIs('supplier.transactions') || request()->routeIs('supplier.purchase') || request()->routeIs('supplier.email') || request()->routeIs('createOrder') || request()->routeIs('orderList')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/purchase-history*') || request()->is('admin/add-stock*') || request()->is('admin/purchase-return-history*') || request()->routeIs('purchase.edit') || request()->routeIs('stockReturnHistory') || request()->routeIs('returnProduct') || request()->routeIs('admin.mothervassel') || request()->routeIs('admin.lightervassel') || request()->routeIs('admin.ghat') || request()->routeIs('allsupplier') || request()->routeIs('supplier.transactions') || request()->routeIs('supplier.purchase') || request()->routeIs('supplier.email') || request()->routeIs('createOrder') || request()->routeIs('orderList')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>
                    Purchase <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @if(in_array('7', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('createOrder') }}" class="nav-link {{ (request()->is('admin/create-order*')) ? 'active' : '' }}">
                        <i class="fas fa-cart-plus nav-icon"></i>
                        <p>Create Order</p>
                    </a>
                </li>
                @endif

                @if(in_array('8', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('orderList') }}" class="nav-link {{ (request()->is('admin/order-list*')) ? 'active' : '' }}">
                        <i class="fas fa-list-alt nav-icon"></i>
                        <p>Order List</p>
                    </a>
                </li>
                @endif

                <li class="nav-item d-none">
                    <a href="{{ route('addStock') }}" class="nav-link {{ (request()->is('admin/add-stock*')) ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart nav-icon"></i>
                        <p>Add new stock</p>
                    </a>
                </li>

                @if(in_array('10', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('productPurchaseHistory') }}" class="nav-link {{ (request()->is('admin/purchase-history*') || request()->routeIs('purchase.edit')) ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar nav-icon"></i>
                        <p>Purchase History</p>
                    </a>
                </li>
                @endif

                @if(in_array('11', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('stockReturnHistory') }}" class="nav-link d-none {{ (request()->is('admin/purchase-return-history') || request()->routeIs('stockReturnHistory')) ? 'active' : '' }}">
                        <i class="fas fa-undo nav-icon"></i>
                        <p>Return History</p>
                    </a>
                </li>
                @endif

                @if(in_array('31', json_decode(auth()->user()->role->permission)))
                <!-- supplier -->
                <li class="nav-item">
                    <a href="{{ route('allsupplier') }}" class="nav-link {{ (request()->is('admin/supplier*') || request()->routeIs('supplier.transactions')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Supplier</p>
                    </a>
                </li>
                @endif

                @if(in_array('13', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.mothervassel') }}" class="nav-link {{ (request()->is('admin/mother-vassel*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ship"></i>
                        <p>Mother Vessel</p>
                    </a>
                </li>
                @endif

                @if(in_array('14', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.lightervassel') }}" class="nav-link {{ (request()->is('admin/lighter-vassel*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-anchor"></i>
                        <p>Lighter Vessel</p>
                    </a>
                </li>
                @endif

                @if(in_array('15', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('admin.ghat') }}" class="nav-link {{ (request()->is('admin/ghat*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Ghat</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @php
            $permissions = [16, 17, 18, 19, 24, 32];
            $userPermissions = json_decode(auth()->user()->role->permission, true);
        @endphp      

        <!-- stock -->
        @if(array_intersect($permissions, $userPermissions))
        <li class="nav-item dropdown {{ (request()->is('admin/stock*') || request()->is('admin/system-losses*') || request()->routeIs('allwarehouse')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ (request()->is('admin/stock*') || request()->is('admin/system-losses*') || request()->routeIs('stockhistory') || request()->routeIs('stockLedger') || request()->routeIs('allwarehouse')) ? 'active' : '' }}">
                <i class="nav-icon fas fa-warehouse"></i>
                <p>
                    Stocks <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                @if(in_array('16', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allstock') }}" class="nav-link {{ (request()->is('admin/stock')) ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Stock List</p>
                    </a>
                </li>
                @endif

                @if(in_array('17', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('stockLedger') }}" class="nav-link {{ (request()->is('admin/stock-ledger') && request()->is('admin/stock-ledger')) ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Stock Ledger</p>
                    </a>
                </li>
                @endif

                @if(in_array('18', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('stockhistory') }}" class="nav-link {{ (request()->is('admin/stocking-history') && !request()->is('admin/add-stock*')) ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>Stocking History</p>
                    </a>
                </li>
                @endif

                @if(in_array('19', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('system-losses.index') }}" class="nav-link {{ (request()->is('admin/system-losses')) ? 'active' : '' }}">
                        <i class="fas fa-undo nav-icon"></i>
                        <p>System Loses</p>
                    </a>
                </li>
                @endif

                @if(in_array('32', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allwarehouse') }}" class="nav-link {{ (request()->is('admin/warehouse*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Warehouse</p>
                    </a>
                </li>
                @endif

            </ul>
        </li>
        @endif

        @php
            $permissions = [20, 21, 22, 23, 24, 25, 26, 27];
            $userPermissions = json_decode(auth()->user()->role->permission, true);
        @endphp
        
        <!-- Sales -->
        @if(array_intersect($permissions, $userPermissions))
        <li class="nav-item dropdown {{ request()->routeIs('inhousesell') || request()->routeIs('allquotations') || request()->routeIs('allcustomer') || request()->routeIs('deliveredorders') || request()->routeIs('returnedorders') || request()->routeIs('processingorders') || request()->routeIs('packedorders') || request()->routeIs('shippedorders') || request()->routeIs('cancelledorders') || request()->routeIs('customer.transactions') || request()->routeIs('order-edit') || request()->routeIs('getallorder') || request()->routeIs('customer.email') || request()->is('admin/*order*') && request()->is('admin/all-inhouse-orders') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link dropdown-toggle {{ request()->routeIs('inhousesell') || request()->routeIs('allquotations') || request()->routeIs('customer.transactions') || request()->routeIs('allcustomer') || request()->routeIs('returnedorders') || request()->routeIs('processingorders') || request()->routeIs('packedorders') || request()->routeIs('customer.email') || request()->routeIs('shippedorders') || request()->routeIs('order-edit')  || request()->routeIs('deliveredorders') || request()->routeIs('cancelledorders') || request()->routeIs('getallorder') || request()->is('admin/*order*') && request()->is('admin/all-inhouse-orders') ? 'active' : '' }}">
                <i class="nav-icon fas fa-truck"></i>
                <p>
                    Sales <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                @if(in_array('24', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('inhousesell') }}" class="nav-link {{ (request()->is('admin/in-house-sell*') || request()->routeIs('order-edit')) ? 'active' : '' }}">
                        <i class="fas fa-industry nav-icon"></i>
                        <p>Sale</p>
                    </a>
                </li>
                @endif

                @if(in_array('25', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('getinhouseorder') }}" class="nav-link {{ (request()->is('admin/all-inhouse-orders*')) ? 'active' : '' }}">
                        <i class="fas fa-industry nav-icon"></i>
                        <p>Sales List</p>
                    </a>
                </li>
                @endif
                
                @if(in_array('20', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('getallorder') }}" class="nav-link d-none {{ request()->is('admin/all-order*') ? 'active' : '' }}">
                        <i class="fas fa-list nav-icon"></i>
                        <p>All Orders</p>
                    </a>
                </li>
                @endif

               {{-- <li class="nav-item">
                    <a href="{{ route('pendingorders') }}" class="nav-link {{ request()->is('admin/pending-orders*') ? 'active' : '' }}">
                        <i class="fas fa-box-open nav-icon"></i>
                        <p>Pending Orders</p>
                    </a>
                </li> --}}

                @if(in_array('21', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('processingorders') }}" class="nav-link {{ request()->is('admin/processing-orders*') ? 'active' : '' }}">
                        <i class="fas fa-cogs nav-icon"></i>
                        <p>Processing Orders</p>
                    </a>
                </li>
                @endif

                @if(in_array('22', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('deliveredorders') }}" class="nav-link {{ request()->is('admin/delivered-orders*') ? 'active' : '' }}">
                        <i class="fas fa-check-circle nav-icon"></i>
                        <p>Delivered Orders</p>
                    </a>
                </li>
                @endif

                {{-- <li class="nav-item">
                    <a href="{{ route('packedorders') }}" class="nav-link {{ request()->is('admin/packed-orders*') ? 'active' : '' }}">
                        <i class="fas fa-boxes nav-icon"></i>
                        <p>Packed Orders</p>
                    </a>
                </li> --}}

                {{-- <li class="nav-item">
                    <a href="{{ route('shippedorders') }}" class="nav-link {{ request()->is('admin/shipped-orders*') ? 'active' : '' }}">
                        <i class="fas fa-shipping-fast nav-icon"></i>
                        <p>Shipped Orders</p>
                    </a>
                </li> --}}

                {{-- <li class="nav-item">
                    <a href="{{ route('returnedorders') }}" class="nav-link {{ request()->is('admin/returned-orders*') ? 'active' : '' }}">
                        <i class="fas fa-undo nav-icon"></i>
                        <p>Returned Products</p>
                    </a>
                </li> --}}

                @if(in_array('23', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('cancelledorders') }}" class="nav-link {{ request()->is('admin/cancelled-orders*') ? 'active' : '' }}">
                        <i class="fas fa-ban nav-icon"></i>
                        <p>Cancelled Orders</p>
                    </a>
                </li>
                @endif

                @if(in_array('26', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allquotations') }}" class="nav-link {{ (request()->is('admin/quotations*')) ? 'active' : '' }} d-none">
                        <i class="fas fa-box nav-icon"></i>
                        <p>Quotation List</p>
                    </a>
                </li>
                @endif

                <!-- customer -->
                @if(in_array('27', json_decode(auth()->user()->role->permission)))
                <li class="nav-item">
                    <a href="{{ route('allcustomer') }}" class="nav-link {{ (request()->is('admin/whole-saler*')) ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Customers</p>
                    </a>
                </li>
                @endif
                
            </ul>
        </li>
        @endif

        @if(in_array('29', json_decode(auth()->user()->role->permission)))
        <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ 
                request()->is('admin/reports') ||
                request()->is('admin/daily-sale') ||
                request()->is('admin/weekly-sale') ||
                request()->is('admin/monthly-sale') ||
                request()->is('admin/date-to-date-sale') ||
                request()->is('admin/daily-purchase') ||
                request()->is('admin/weekly-purchase') ||
                request()->is('admin/monthly-purchase') ||
                request()->is('admin/date-to-date-purchase')
                ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Reports</p>
            </a>
        </li>
        @endif
        
        <li class="mb-5"></li>
        <li class="mb-5"></li>
        <li class="mb-5"></li>
        
    </ul>
  </nav>