@extends('admin.layouts.admin')

@section('content')
<section class="content py-3 px-5">
    <a href="{{ route('allproduct') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <div class="card card-solid">
        <div class="card-body">
            <div class="row">
            <div class="col-12 col-sm-6">
                <h3 class="d-inline-block d-sm-none">{{ $product->name }}</h3>
                <div class="col-10">
                <img src="{{ asset('/images/products/' . $product->feature_image) }}" class="product-image" alt="Product Image">
                </div>
                <div class="col-12 product-image-thumbs">
                    @foreach($product->colors as $productColor)
                        @isset($productColor->color)
                            <div class="product-image-thumb">
                                <img src="{{ asset($productColor->image) }}" data-color-image="{{ asset($productColor->image) }}" class="img-thumbnail" alt="{{ $productColor->color->color }}">
                            </div>
                        @endisset
                    @endforeach
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <h3 class="my-3">{{ $product->name }}</h3>
                <p>{!! $product->short_description !!}</p>

                <hr>

                @if($product->product_code)
                    <h4>Code: <span>{{ $product->product_code }}</span></h4>
                @endif
                @if($product->category)
                    <h4>Category: <span>{{ $product->category->name }}</span></h4>
                @endif
                @if($product->subCategory)
                    <h4>Sub-Category: <span>{{ $product->subCategory->name }}</span></h4>
                @endif
                @if($product->brand)
                    <h4>Brand: <span>{{ $product->brand->name }}</span></h4>
                @endif
                @if($product->productModel)
                    <h4>Model: <span>{{ $product->productModel->name }}</span></h4>
                @endif
                <hr>
                <h4>Available Colors</h4>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    @foreach($product->colors as $productColor)
                        @isset($productColor->color)
                            @php
                                $color = $productColor->color;
                            @endphp
                            <label class="btn btn-default text-center {{ $loop->first ? 'active' : '' }}">
                                <input type="radio" name="color_option" id="color_option_{{ $color->id }}" autocomplete="off" {{ $loop->first ? 'checked' : '' }}>
                                {{ $color->color ?? 'N/A' }} 
                                <br>
                                <i class="fas fa-circle fa-2x" style="color: {{ $color->color_code ?? '#000' }}"></i>
                            </label>
                        @endisset
                    @endforeach
                </div>

                @if($product->sizes->count() > 0)
                    <h4 class="mt-3">Available Sizes</h4>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        @foreach($product->sizes as $productSize)
                            @php
                                $sizeId = $productSize->id;
                                $sizeName = $productSize->size;
                            @endphp
                            <label class="btn btn-default text-center {{ $loop->first ? 'active' : '' }}">
                                <input type="radio" name="size_option" id="size_option_{{ $sizeId }}" autocomplete="off" {{ $loop->first ? 'checked' : '' }}>
                                <span class="text-xl">{{ $sizeName ?? 'N/A' }}</span>
                                <br>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="bg-gray py-2 px-3 mt-4">
                <h2 class="mb-0">
                    {{ $product->price }} {{ $currency }}
                </h2>
                <h4 class="mt-0">
                </h4>
                </div>

                <div class="mt-4 product-share">
                    <!-- Facebook Share -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}" class="text-gray" target="_blank">
                        <i class="fab fa-facebook-square fa-2x"></i>
                    </a>
                    
                    <!-- Twitter Share -->
                    <a href="https://twitter.com/intent/tweet?text=Check%20out%20this%20product!&url={{ urlencode(Request::fullUrl()) }}" class="text-gray" target="_blank">
                        <i class="fab fa-twitter-square fa-2x"></i>
                    </a>
                    
                    <!-- Email Share -->
                    <a href="mailto:?subject=Check%20out%20this%20product&body={{ urlencode(Request::fullUrl()) }}" class="text-gray">
                        <i class="fas fa-envelope-square fa-2x"></i>
                    </a>
                    
                </div>

            </div>
            </div>
            <div class="row mt-4">
            <nav class="w-100">
                <div class="nav nav-tabs" id="product-tab" role="tablist">
                <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Description</a>
                <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Comments</a>
                <a class="nav-item nav-link" id="product-rating-tab" data-toggle="tab" href="#product-rating" role="tab" aria-controls="product-rating" aria-selected="false">Rating</a>
                </div>
            </nav>
            <div class="tab-content p-3" id="nav-tabContent">
                <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab"> {!! $product->long_description !!} </div>
                <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab"></div>
                <div class="tab-pane fade" id="product-rating" role="tabpanel" aria-labelledby="product-rating-tab"></div>
            </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')

<script>
  $(document).ready(function() {
    $('.product-image-thumb').on('click', function () {
      var $imageElement = $(this).find('img');
      $('.product-image').prop('src', $imageElement.attr('src'));
      $('.product-image-thumb.active').removeClass('active');
      $(this).addClass('active');
    });
  });
</script>

@endsection