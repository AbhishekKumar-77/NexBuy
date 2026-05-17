// content.js
// Runs on GeM portal product pages

function injectNexBuyWidget() {
    // Check if we are on a product page by looking for the title
    const productTitleEl = document.querySelector('h1.title');
    if (!productTitleEl) return;

    const productName = productTitleEl.innerText.trim();

    // Create the floating widget
    const widget = document.createElement('div');
    widget.id = 'nexbuy-floating-widget';
    widget.innerHTML = `
        <div class="nexbuy-header">
            <span class="logo">Nex<b>Buy</b></span> Insights
        </div>
        <div class="nexbuy-body">
            <p>Comparing prices for:</p>
            <strong class="item-name">${productName.substring(0, 30)}...</strong>
            
            <div class="prices">
                <div class="platform">
                    <span class="icon amz">A</span>
                    <div class="details">
                        <span class="label">Amazon</span>
                        <span class="price">₹${Math.floor(Math.random() * (50000 - 10000) + 10000).toLocaleString('en-IN')}</span>
                    </div>
                </div>
                <div class="platform">
                    <span class="icon flp">F</span>
                    <div class="details">
                        <span class="label">Flipkart</span>
                        <span class="price">₹${Math.floor(Math.random() * (50000 - 10000) + 10000).toLocaleString('en-IN')}</span>
                    </div>
                </div>
            </div>
            
            <a href="http://localhost:8000/search?q=${encodeURIComponent(productName)}" target="_blank" class="nexbuy-btn">View Detailed CS Report</a>
        </div>
    `;

    document.body.appendChild(widget);
}

// Wait for page to fully load
setTimeout(injectNexBuyWidget, 1500);
