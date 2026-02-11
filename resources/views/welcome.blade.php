<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product CRUD Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-2xl p-6 sm:p-8 lg:p-10">
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-800 mb-6 sm:mb-8 text-center">
            🛍️ Product CRUD Demo
        </h1>

        @if(session('success'))
            <div class="no-print bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Toggle Add Form Button -->
        <button onclick="toggleAddForm()" class="no-print bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 mb-6">
            ➕ Add New Product
        </button>

        <!-- Add Product Form (Hidden by default) -->
        <div class="no-print bg-gray-50 rounded-xl p-6 sm:p-8 mb-6 sm:mb-8" id="addFormSection" style="display: none;">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-6">➕ Add New Product</h2>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                    <div>
                        <label for="name" class="block text-gray-700 font-semibold mb-2">Product Name</label>
                        <input type="text" id="name" name="name" required placeholder="Enter product name"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition-colors">
                        @error('name')
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-gray-700 font-semibold mb-2">Price (USD)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required placeholder="0.00"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition-colors">
                        @error('price')
                            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all">
                        Add Product
                    </button>
                    <button type="button" onclick="toggleAddForm()" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">📦 Products List</h2>
        
        @if($products->count() > 0)
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto rounded-lg shadow">
                <table class="w-full">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Product Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Price</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr id="row-{{ $product->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium no-print">
                                    <div class="flex flex-wrap gap-2">
                                        <button onclick="printProduct('{{ $product->name }}', '{{ number_format($product->price, 2) }}')" 
                                            class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                            🖨️ Print
                                        </button>
                                        <button onclick="toggleEdit({{ $product->id }})" 
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                            ✏️ Edit
                                        </button>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Edit Form Row (Hidden by default) -->
                            <tr class="edit-form hidden" id="edit-{{ $product->id }}">
                                <td colspan="4" class="px-6 py-4 bg-yellow-50">
                                    <form action="{{ route('products.update', $product) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                                            <div>
                                                <label class="block text-gray-700 font-semibold mb-2 text-sm">Product Name</label>
                                                <input type="text" name="name" value="{{ $product->name }}" required
                                                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-gray-700 font-semibold mb-2 text-sm">Price (USD)</label>
                                                <input type="number" name="price" value="{{ $product->price }}" step="0.01" min="0" required
                                                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                                            </div>
                                            <div class="flex gap-2 col-span-1 sm:col-span-2">
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                                                    💾 Save
                                                </button>
                                                <button type="button" onclick="toggleEdit({{ $product->id }})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                                                    ❌ Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                @foreach($products as $product)
                    <div id="card-{{ $product->id }}" class="bg-white rounded-lg shadow-md p-5 border border-gray-200">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $product->name }}</h3>
                                <p class="text-2xl font-bold text-indigo-600">${{ number_format($product->price, 2) }}</p>
                                <p class="text-xs text-gray-500 mt-1">ID: {{ $product->id }}</p>
                            </div>
                        </div>
                        
                        <div class="no-print flex flex-wrap gap-2 mt-4">
                            <button onclick="printProduct('{{ $product->name }}', '{{ number_format($product->price, 2) }}')" 
                                class="flex-1 bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                🖨️ Print
                            </button>
                            <button onclick="toggleEditMobile({{ $product->id }})" 
                                class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                ✏️ Edit
                            </button>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this product?');" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>

                        <!-- Mobile Edit Form -->
                        <div id="edit-mobile-{{ $product->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <form action="{{ route('products.update', $product) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Product Name</label>
                                        <input type="text" name="name" value="{{ $product->name }}" required
                                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-semibold mb-2 text-sm">Price (USD)</label>
                                        <input type="number" name="price" value="{{ $product->price }}" step="0.01" min="0" required
                                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                                            💾 Save
                                        </button>
                                        <button type="button" onclick="toggleEditMobile({{ $product->id }})" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                                            ❌ Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 text-xl">No products found. Add your first product above! 🎯</p>
            </div>
        @endif
    </div>

    <script>
        function toggleAddForm() {
            const formSection = document.getElementById('addFormSection');
            if (formSection.style.display === 'none') {
                formSection.style.display = 'block';
            } else {
                formSection.style.display = 'none';
            }
        }

        function toggleEdit(productId) {
            const editRow = document.getElementById('edit-' + productId);
            const normalRow = document.getElementById('row-' + productId);
            
            if (editRow.classList.contains('active')) {
                editRow.classList.remove('active');
                editRow.classList.add('hidden');
                normalRow.style.display = 'table-row';
            } else {
                // Close any other open edit forms
                document.querySelectorAll('.edit-form.active').forEach(form => {
                    form.classList.remove('active');
                    form.classList.add('hidden');
                });
                document.querySelectorAll('tbody tr:not(.edit-form)').forEach(row => {
                    row.style.display = 'table-row';
                });
                
                // Open this edit form
                editRow.classList.add('active');
                editRow.classList.remove('hidden');
                normalRow.style.display = 'none';
            }
        }

        function toggleEditMobile(productId) {
            const editForm = document.getElementById('edit-mobile-' + productId);
            
            if (editForm.classList.contains('hidden')) {
                // Close any other open edit forms
                document.querySelectorAll('[id^="edit-mobile-"]').forEach(form => {
                    form.classList.add('hidden');
                });
                
                // Open this edit form
                editForm.classList.remove('hidden');
            } else {
                editForm.classList.add('hidden');
            }
        }

        function printProduct(productName, productPrice) {
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            
            // Write your print template
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Product</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            padding: 20px;
                        }
                        .print-content {
                            max-width: 600px;
                            margin: 0 auto;
                        }
                        h2 {
                            color: #333;
                            margin-bottom: 20px;
                        }
                        .product-info {
                            margin: 20px 0;
                            font-size: 16px;
                        }
                        .product-info p {
                            margin: 10px 0;
                            padding: 10px;
                            background: #f5f5f5;
                            border-left: 4px solid #667eea;
                        }
                        .label {
                            font-weight: bold;
                            color: #555;
                        }
                    </style>
                </head>
                <body>
                    <div class="print-content">
                        <h2>Product Details</h2>
                        <div class="product-info">
                            <p><span class="label">Product Name:</span> ${productName}</p>
                            <p><span class="label">Price:</span> $${productPrice}</p>
                        </div>
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.focus();
            
            // Wait for content to load then print
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }
    </script>
</body>
</html>
