@php
  use App\Models\Category;
  $categories = Category::where('status', 1)->latest()->get();
@endphp

<div class="modal fade" id="newProductModal" tabindex="-1" role="dialog" aria-labelledby="newProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newProductModalLabel">Add New Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="productForm">
          <div class="row">
            <div class="form-group col-md-4">
              <label for="name">Product Name <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="product_name" name="name" placeholder="Ex. Stylish Running Shoes">
            </div>
            <div class="form-group col-md-4">
              <label for="product_code">Product Code <span style="color: red;">*</span></label>
              <input type="text" class="form-control" id="product_code" name="product_code" placeholder="Ex. PRD-12345">
              <span id="productCodeError" class="text-danger"></span>
            </div>
            <div class="form-group col-md-4">
              <label for="category">Category
              </label>
              <select class="form-control select2" id="category" name="category_id">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <button type="button" class="btn btn-success" id="saveProductBtn">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>