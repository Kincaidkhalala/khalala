import React, { useState } from "react";

// Product form component for adding and editing
const ProductForm = ({ product, onSave, onCancel }) => {
  const [formData, setFormData] = useState(
    product || {
      name: "",
      description: "",
      category: "",
      price: "",
      quantity: "",
    }
  );

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (
      formData.name &&
      formData.description &&
      formData.category &&
      formData.price &&
      formData.quantity
    ) {
      onSave({
        ...formData,
        price: parseFloat(formData.price),
        quantity: parseInt(formData.quantity, 10),
      });
    } else {
      alert("Please fill all fields.");
    }
  };

  return (
    <form onSubmit={handleSubmit} className="product-form">
      <h3>{product ? "Edit Product" : "Add New Product"}</h3>
      <input
        type="text"
        name="name"
        placeholder="Product Name"
        value={formData.name}
        onChange={handleChange}
      />
      <textarea
        name="description"
        placeholder="Description"
        value={formData.description}
        onChange={handleChange}
      />
      <input
        type="text"
        name="category"
        placeholder="Category"
        value={formData.category}
        onChange={handleChange}
      />
      <input
        type="number"
        name="price"
        placeholder="Price"
        step="0.01"
        value={formData.price}
        onChange={handleChange}
      />
      <input
        type="number"
        name="quantity"
        placeholder="Initial Quantity"
        value={formData.quantity}
        onChange={handleChange}
      />
      <button type="submit">Save</button>
      <button type="button" onClick={onCancel} style={{ marginLeft: 10 }}>
        Cancel
      </button>
    </form>
  );
};

const Inventory = () => {
  // State for product list
  const [products, setProducts] = useState([]);

  // State for editing product (null if adding new)
  const [editingProductIndex, setEditingProductIndex] = useState(null);

  // Flag to show/hide form
  const [showForm, setShowForm] = useState(false);

  // Add or update product
  const handleSaveProduct = (productData) => {
    if (editingProductIndex !== null) {
      // Update product
      setProducts((prev) => {
        const newProducts = [...prev];
        newProducts[editingProductIndex] = productData;
        return newProducts;
      });
    } else {
      // Add new product
      setProducts((prev) => [...prev, productData]);
    }
    setShowForm(false);
    setEditingProductIndex(null);
  };

  // Delete product
  const handleDeleteProduct = (index) => {
    if (window.confirm("Are you sure you want to delete this product?")) {
      setProducts((prev) => prev.filter((_, i) => i !== index));
    }
  };

  // Track stock (adding or subtracting quantity)
  const handleStockChange = (index, change) => {
    setProducts((prev) => {
      const newProducts = [...prev];
      const newQty = newProducts[index].quantity + change;
      if (newQty < 0) {
        alert("Stock level cannot be negative.");
        return prev;
      }
      newProducts[index].quantity = newQty;
      return newProducts;
    });
  };

  return (
    <div className="inventory">
      <h1>Wings Cafe Stock Inventory System</h1>

      {!showForm && (
        <button onClick={() => setShowForm(true)}>Add New Product</button>
      )}

      {showForm && (
        <ProductForm
          product={products[editingProductIndex] || null}
          onSave={handleSaveProduct}
          onCancel={() => {
            setShowForm(false);
            setEditingProductIndex(null);
          }}
        />
      )}

      <h2>Product List</h2>
      {products.length === 0 ? (
        <p>No products available.</p>
      ) : (
        <table border="1" cellPadding="8" cellSpacing="0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Category</th>
              <th>Price ($)</th>
              <th>Quantity in Stock</th>
              <th>Actions</th>
              <th>Stock Management</th>
            </tr>
          </thead>
          <tbody>
            {products.map((prod, index) => (
              <tr
                key={index}
                style={{
                  backgroundColor: prod.quantity < 5 ? "#ffcccc" : "transparent",
                }}
              >
                <td>{prod.name}</td>
                <td>{prod.description}</td>
                <td>{prod.category}</td>
                <td>{prod.price.toFixed(2)}</td>
                <td>{prod.quantity}</td>
                <td>
                  <button
                    onClick={() => {
                      setEditingProductIndex(index);
                      setShowForm(true);
                    }}
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => handleDeleteProduct(index)}
                    style={{ marginLeft: 10 }}
                  >
                    Delete
                  </button>
                </td>
                <td>
                  <button onClick={() => handleStockChange(index, +1)}>+</button>
                  <button
                    onClick={() => handleStockChange(index, -1)}
                    style={{ marginLeft: 5 }}
                  >
                    -
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </div>
  );
};

export default Inventory;
