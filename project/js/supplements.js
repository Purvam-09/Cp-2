document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const productCards = document.querySelectorAll('.product-card');
    const categoryFilters = document.querySelectorAll('input[name="category"]');
    const goalFilters = document.querySelectorAll('input[name="goal"]');
    const dietFilters = document.querySelectorAll('input[name="diet"]');
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const sortBySelect = document.getElementById('sort-by');
    const productCount = document.getElementById('product-count');
    
    // Update price range display
    if (priceRange && priceValue) {
        priceRange.addEventListener('input', function() {
            priceValue.textContent = `$${this.value}`;
        });
    }
    
    // Clear all filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            // Clear all checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Reset price range
            if (priceRange) {
                priceRange.value = 100;
                priceValue.textContent = '$100';
            }
            
            // Show all products
            filterProducts();
        });
    }
    
    // Apply filters
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            filterProducts();
        });
    }
    
    // Sort products
    if (sortBySelect) {
        sortBySelect.addEventListener('change', function() {
            sortProducts(this.value);
        });
    }
    
    // Function to filter products
    function filterProducts() {
        let filteredProducts = [...productCards];
        let selectedCategories = [];
        let selectedGoals = [];
        let selectedDiets = [];
        let maxPrice = priceRange ? parseFloat(priceRange.value) : 100;
        
        // Get selected categories
        categoryFilters.forEach(filter => {
            if (filter.checked) {
                selectedCategories.push(filter.value);
            }
        });
        
        // Get selected goals
        goalFilters.forEach(filter => {
            if (filter.checked) {
                selectedGoals.push(filter.value);
            }
        });
        
        // Get selected diets
        dietFilters.forEach(filter => {
            if (filter.checked) {
                selectedDiets.push(filter.value);
            }
        });
        
        // Apply filters
        filteredProducts = filteredProducts.filter(product => {
            const category = product.dataset.category;
            const goals = product.dataset.goal ? product.dataset.goal.split(',') : [];
            const diets = product.dataset.diet ? product.dataset.diet.split(',') : [];
            const price = parseFloat(product.dataset.price);
            
            // Check if product passes all filters
            const passesCategory = selectedCategories.length === 0 || selectedCategories.includes(category);
            const passesGoal = selectedGoals.length === 0 || selectedGoals.some(goal => goals.includes(goal));
            const passesDiet = selectedDiets.length === 0 || selectedDiets.every(diet => diets.includes(diet));
            const passesPrice = price <= maxPrice;
            
            return passesCategory && passesGoal && passesDiet && passesPrice;
        });
        
        // Update product display
        productCards.forEach(product => {
            if (filteredProducts.includes(product)) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
        
        // Update product count
        if (productCount) {
            productCount.textContent = filteredProducts.length;
        }
        
        // Apply current sort
        if (sortBySelect) {
            sortProducts(sortBySelect.value);
        }
    }
    
    // Function to sort products
    function sortProducts(sortBy) {
        const productsContainer = document.querySelector('.supplements-grid');
        const visibleProducts = Array.from(productCards).filter(product => product.style.display !== 'none');
        
        visibleProducts.sort((a, b) => {
            switch (sortBy) {
                case 'price-asc':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-desc':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'rating':
                    // Using the number of filled stars as a proxy for rating
                    const ratingA = a.querySelectorAll('.fa-star').length;
                    const ratingB = b.querySelectorAll('.fa-star').length;
                    return ratingB - ratingA;
                case 'newest':
                    // For demo purposes, we'll just use alphabetical order
                    return a.querySelector('h3').innerText < b.querySelector('h3').innerText ? -1 : 1;
                default: // popular
                    // For demo purposes, featured items first, then alphabetical
                    const hasPromoA = a.querySelector('.product-badge') !== null;
                    const hasPromoB = b.querySelector('.product-badge') !== null;
                    if (hasPromoA && !hasPromoB) return -1;
                    if (!hasPromoA && hasPromoB) return 1;
                    return a.querySelector('h3').innerText < b.querySelector('h3').innerText ? -1 : 1;
            }
        });
        
        // Reorder products in the DOM
        visibleProducts.forEach(product => {
            productsContainer.appendChild(product);
        });
    }
    
    // Initialize filters from URL params if any
    function initializeFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        
        if (category) {
            const categoryCheckbox = document.querySelector(`input[name="category"][value="${category}"]`);
            if (categoryCheckbox) {
                categoryCheckbox.checked = true;
                filterProducts();
            }
        }
    }
    
    initializeFiltersFromURL();
});