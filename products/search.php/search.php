<?php
require_once '../main/config.php';

$pageSize = 6; // Number of products per page

// Handle AJAX JSON response
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    if ($query === '') {
        echo json_encode(['products' => [], 'total' => 0, 'page' => $page]);
        exit;
    }

    // Get total count for pagination
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE name LIKE ?");
    $searchTerm = '%' . $query . '%';
    $countStmt->bind_param('s', $searchTerm);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = $countResult->fetch_row()[0];

    $offset = ($page - 1) * $pageSize;

    // Get paginated products
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? LIMIT ? OFFSET ?");
    $stmt->bind_param('sii', $searchTerm, $pageSize, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($product = $result->fetch_assoc()) {
        $products[] = $product;
    }

    echo json_encode([
        'products' => $products,
        'total' => $total,
        'page' => $page,
        'pageSize' => $pageSize,
        'totalPages' => ceil($total / $pageSize)
    ]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search - Deals by Keith</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/css/style.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <!-- Your navbar here -->
</nav>

<div class="container mt-4">
    <h2>Search Products</h2>
    <form id="searchForm" class="d-flex mb-3" onsubmit="return false;">
        <input id="searchInput" class="form-control me-2" type="search" name="q" placeholder="Search products" autocomplete="off" />
        <button class="btn btn-primary" type="submit" onclick="performSearch()">Search</button>
    </form>
    <div id="results" class="row"></div>
    <p id="noResults" class="mt-3" style="display:none;">No products found.</p>

    <nav aria-label="Page navigation" class="mt-4">
        <ul id="pagination" class="pagination justify-content-center"></ul>
    </nav>
</div>

<script>
const searchInput = document.getElementById('searchInput');
const resultsContainer = document.getElementById('results');
const noResultsMessage = document.getElementById('noResults');
const paginationContainer = document.getElementById('pagination');

let currentPage = 1;
let currentQuery = '';

// Escape HTML special characters
function escapeHtml(text) {
    return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

function createProductCard(product) {
    return `
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <img src="/media/${product.image}" class="card-img-top" alt="${escapeHtml(product.name)}" />
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">${escapeHtml(product.name)}</h5>
                <p class="card-text">KSh ${product.price}</p>
                <a href="product.php?id=${product.id}" class="btn btn-primary mt-auto">View Details</a>
            </div>
        </div>
    </div>
    `;
}

function updatePagination(totalPages) {
    paginationContainer.innerHTML = '';

    if (totalPages <= 1) return;

    for (let i = 1; i <= totalPages; i++) {
        const activeClass = (i === currentPage) ? 'active' : '';
        const li = document.createElement('li');
        li.className = `page-item ${activeClass}`;
        li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
        paginationContainer.appendChild(li);
    }

    // Add click listeners
    paginationContainer.querySelectorAll('a.page-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const page = parseInt(e.target.getAttribute('data-page'));
            if (page !== currentPage) {
                currentPage = page;
                performSearch();
            }
        });
    });
}

async function performSearch() {
    const query = searchInput.value.trim();
    currentQuery = query;
    if (!query) {
        resultsContainer.innerHTML = '';
        noResultsMessage.style.display = 'none';
        paginationContainer.innerHTML = '';
        return;
    }

    try {
        const response = await fetch(`search.php?ajax=1&q=${encodeURIComponent(query)}&page=${currentPage}`);
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();

        if (data.products.length === 0) {
            resultsContainer.innerHTML = '';
            noResultsMessage.style.display = 'block';
            paginationContainer.innerHTML = '';
        } else {
            noResultsMessage.style.display = 'none';
            resultsContainer.innerHTML = data.products.map(createProductCard).join('');
            updatePagination(data.totalPages);
        }
    } catch (error) {
        console.error('Fetch error:', error);
        resultsContainer.innerHTML = `<p class="text-danger">An error occurred while searching.</p>`;
        paginationContainer.innerHTML = '';
    }
}

// Debounce function to limit search frequency
let debounceTimeout;
searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        currentPage = 1;  // Reset page on new search
        performSearch();
    }, 300);
});

// Perform search on page load if 'q' parameter present
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const q = urlParams.get('q');
    if (q) {
        searchInput.value = q;
        performSearch();
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
