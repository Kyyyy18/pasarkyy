// Simple and focused script for MarketVision
document.addEventListener('DOMContentLoaded', function() {
    console.log('MarketVision script loaded');
    
    // Basic mobile navigation
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Price filter functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            productCards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // MODAL FUNCTIONALITY - THE MAIN FEATURE
    const modal = document.getElementById('price-modal');
    if (!modal) {
        console.error('Modal element not found!');
        return;
    }
    
    const detailButtons = document.querySelectorAll('.btn-details');
    const closeBtn = modal.querySelector('.close-btn');
    const modalTitle = document.getElementById('modal-product-name');
    const modalBody = document.getElementById('modal-price-details');
    
    const markets = ['Pasar Cepiring', 'Pasar Kendal', 'Pasar Kaliwungu', 'Pasar Weleri'];
    
    console.log('Found', detailButtons.length, 'detail buttons');
    
    // Add click event to each detail button
    detailButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Detail button clicked');
            
            try {
                const productName = this.getAttribute('data-product-name');
                const detailsData = this.getAttribute('data-details');
                const details = JSON.parse(detailsData);
                
                console.log('Product:', productName);
                console.log('Details:', details);
                
                // Set modal title
                modalTitle.textContent = `Rincian Harga: ${productName}`;
                
                // Create price map
                const priceMap = {};
                details.forEach(item => {
                    priceMap[item.nama_pasar] = item;
                });
                
                // Build HTML content
                let html = '';
                markets.forEach(market => {
                    html += '<div class="price-item">';
                    html += `<span class="market-name">${market}</span>`;
                    
                    if (priceMap[market]) {
                        const price = priceMap[market];
                        const formattedPrice = new Intl.NumberFormat('id-ID').format(price.harga);
                        html += `<span class="price-value">Rp ${formattedPrice} / ${price.satuan}</span>`;
                    } else {
                        html += '<span class="price-unavailable">Harga tidak tersedia</span>';
                    }
                    
                    html += '</div>';
                });
                
                modalBody.innerHTML = html;
                modal.style.display = 'block';
                
                console.log('Modal opened successfully');
                
            } catch (error) {
                console.error('Error parsing data:', error);
                modalBody.innerHTML = '<p>Error: Tidak dapat memuat data harga</p>';
                modal.style.display = 'block';
            }
        });
    });
    
    // Close modal function
    function closeModal() {
        modal.style.display = 'none';
    }
    
    // Close button event
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    // Close on outside click
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
    
    console.log('All event listeners attached');
});