@php
        $brands = \App\Models\Brand::select('id', 'name')->where('status', 1)->orderby('id','DESC')->get();
        $product_models = \App\Models\ProductModel::select('id', 'name')->where('status', 1)->orderby('id','DESC')->get();
        $groups = \App\Models\Group::select('id', 'name')->where('status', 1)->orderby('id','DESC')->get();
        $units = \App\Models\Unit::select('id', 'name')->where('status', 1)->orderby('id','DESC')->get();
        $categories = \App\Models\Category::select('id', 'name')->where('status', 1)->orderby('id','DESC')->get();
        $subCategories = \App\Models\SubCategory::select('id', 'name', 'category_id')->where('status', 1)->orderby('id','DESC')->get();
        $sizes = \App\Models\Size::select('id', 'size')->orderby('id','DESC')->where('status', 1)->get();
        $colors = \App\Models\Color::select('id', 'color', 'color_code')->where('status', 1)->orderby('id','DESC')->get();
@endphp

<!-- New Product Modal -->
<div class="modal fade" id="newProductModal" tabindex="-1" aria-labelledby="newProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newProductModalLabel">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- New Product Form -->
                
                <div class="card-body">
                    <div class="ermsg"></div>
                    <form id="productCreateThisForm">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">Product Name <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Ex. Stylish Running Shoes">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="price">Product Code <span style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="product_code" name="product_code" placeholder="Ex. PRD-12345">
                                <span id="productCodeError" class="text-danger"></span>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price" placeholder="Ex. 1000">
                            </div>
                            <div class="form-group col-md-2 d-none">
                                <label for="size_ids">Sizes</label>
                                <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSizeModal">Add New</span>
                                <select class="form-control select2" id="size_ids" name="size_ids[]" multiple="multiple" data-placeholder="Select sizes">
                                    @foreach($sizes as $size)
                                    <option value="{{ $size->id }}">{{ $size->size }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2 d-none">
                                <label for="sku">Sku</label>
                                <input type="number" class="form-control" id="sku" name="sku" placeholder="Ex. 123">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="short_description">Short Description <span style="color: red;">*</span></label>
                                <textarea class="form-control" id="short_description" name="short_description"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description">Long Description <span style="color: red;">*</span></label>
                                <textarea class="form-control" id="long_description" name="long_description"></textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="category">Category
                                    <span style="color: red;">*</span>
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                </label>
                                <select class="form-control" id="category" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="subcategory">
                                    Sub Category
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSubCategoryModal">Add New</span>
                                </label>
                                <select class="form-control" id="subcategory" name="sub_category_id">
                                    <option value="">Select Sub Category</option>
                                    @foreach($subCategories as $subcategory)
                                    <option class="subcategory-option category-{{ $subcategory->category_id }}" value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="brand">
                                    Brand
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addBrandModal">Add New</span>
                                </label>
                                <select class="form-control" id="brand" name="brand_id">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="model">Model
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addModelModal">Add New</span>
                                </label>
                                <select class="form-control" id="model" name="product_model_id">
                                    <option value="">Select Model</option>
                                    @foreach($product_models as $model)
                                    <option value="{{ $model->id }}">{{ $model->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="unit">
                                    Unit <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addUnitModal">Add New</span>
                                </label>
                                <select class="form-control" id="unit" name="unit_id">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="group">
                                    Group <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addGroupModal">Add New</span>
                                </label>
                                <select class="form-control" id="group" name="group_id">
                                    <option value="">Select Group</option>
                                    @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Feature Image part start -->
                            <div class="form-group col-md-5">
                                <label for="feature-img">Feature Image <span style="color: red;">*</span></label>
                                <input type="file" class="form-control-file" id="feature-img"  name="feature_image" accept="image/*">
                                <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                            </div>
                            <!-- Feature Image part end -->

                            <div class="d-none">
                            <div class="form-group col-md-1">
                                <label for="is_whole_sale">Whole Sale</label>
                                <input type="checkbox" class="form-control" id="is_whole_sale" name="is_whole_sale" value="1" checked>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="is_featured">Featured</label>
                                <input type="checkbox" class="form-control" id="is_featured" name="is_featured" value="1">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="is_recent">Recent</label>
                                <input type="checkbox" class="form-control" id="is_recent" name="is_recent" value="1">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="is_new_arrival">New Arriv.</label>
                                <input type="checkbox" class="form-control" id="is_new_arrival" name="is_new_arrival" value="1">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="is_top_rated">Top Rated</label>
                                <input type="checkbox" class="form-control" id="is_top_rated" name="is_top_rated" value="1">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="is_popular">Popular</label>
                                <input type="checkbox" class="form-control" id="is_popular" name="is_popular" value="1">
                            </div>
                            <div class="form-group col-md-1">
                                <label for="is_trending">Trending</label>
                                <input type="checkbox" class="form-control" id="is_trending" name="is_trending" value="1">
                            </div>
                            </div>
                        </div>

                        <div class="form-row d-none">
                            <div class="form-group col-md-5">
                                <label for="color_id">Select Color</label>
                                <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span>
                                <select class="form-control" name="color_id[]" id="color_id">
                                    <option value="">Choose Color</option>
                                    @foreach($colors as $color)
                                    <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};">
                                        {{ $color->color }} ({{ $color->color_code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="image">Select Image</label>
                                <input type="file" class="form-control" name="image[]" accept="image/*">
                            </div>
                            <div class="form-group col-md-1">
                                <label>Action</label>
                                <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div id="dynamic-rows"></div>

                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" id="productaddBtn" class="btn btn-secondary" value="Create">Create</button>
                    <div id="loader" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </div>
                </div>
                


            </div>
        </div>
    </div>
</div>