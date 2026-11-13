<div class="form-group">
    <label for="name">Tên sản phẩm</label>
    <input class="form-input" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" maxlength="255" required>
</div>
<div class="form-group">
    <label for="category_id">Danh mục</label>
    <select class="form-input" id="category_id" name="category_id" required>
        <option value="">Chọn danh mục</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="price">Giá bán (VNĐ)</label>
    <input class="form-input" id="price" type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="0.01" step="1000" required>
</div>
<div class="form-group">
    <label for="quantity">Số lượng tồn kho</label>
    <input class="form-input" id="quantity" type="number" name="quantity" value="{{ old('quantity', $product->quantity ?? 0) }}" min="0" step="1" required>
</div>
<div class="form-group">
    <label for="image">Đường dẫn hình ảnh</label>
    <input class="form-input" id="image" type="text" name="image" value="{{ old('image', $product->image ?? '') }}" maxlength="2048" placeholder="https://... hoặc đường dẫn ảnh trong storage">
</div>
<div class="form-group">
    <label for="description">Mô tả</label>
    <textarea class="form-input" id="description" name="description" rows="5" maxlength="5000">{{ old('description', $product->description ?? '') }}</textarea>
</div>
