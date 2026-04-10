let products = [];  // Store CSV data

// Load and parse CSV file
function loadCSV() {
    Papa.parse("data/product.csv", {
        download: true,
        header: true,
        skipEmptyLines: true,
        complete: function(results) {
            products = results.data;
            displayItems(products);
            loadLastSearch();
        }
    });
}

// Fetch product image from visualcontent.csv
function getProductPicture(productID) {
    return new Promise((resolve, reject) => {
        fetch(`api/productpicture/?id=${productID}`)
            .then(response => response.json())
            .then(data => {
                const result = `<img src="${data.imagePath}" alt="${data.alt}" class='box-img'>`;
                resolve(result);
            })
            .catch(error => {
                resolve(`<img src="default.jpg" alt="No image available">`);
            });
    });
}


function getAverageRating(productID) {
    return new Promise((resolve, reject) => {
        fetch('api/comment')
            .then(response => response.json())
            .then(comments => {
                const matchingComments = comments.filter(p => Number(p.ProductID) === Number(productID));
                if (matchingComments.length === 0) {
                    resolve("No Review");
                    return;
                }
                const totalRating = matchingComments.reduce((sum, comment) => sum + Number(comment.Rating), 0);
                const averageRating = totalRating / matchingComments.length;
                resolve(Number(averageRating.toFixed(1)));
            })
            .catch(error => {
                resolve("No Review");
            });
    });
}


// Display filtered results
async function displayItems(filteredProducts) {
    const resultList = document.getElementById("results");
    resultList.innerHTML = "";

    try {
        const response = await fetch('api/product');
        const fetchedProducts = await response.json();
        products = fetchedProducts; // assuming you use this globally elsewhere

        const productHTMLArray = await Promise.all(filteredProducts.map(async (product) => {
            const productImage = await getProductPicture(product.ProductID);
            const averageRating = await getAverageRating(product.ProductID);
            return `
                <a href='product/?id=${product.ProductID}' class='thumbnail-link'>
                    <div class='thumbnail'>
                        <div class='thumbnailpic'>
                            ${productImage} 
                        </div>
                        <div class='detail'>
                            <h3>${product.Name}</h3>
                            <p class='shop'>${product.ShopName}</p>
                            <p>Price: $${product.Price}</p>
                            <p>Rating: ${averageRating} <i class='fa-solid fa-star'></i></p>
                            <p>Keyword: ${product.Keyword}</p>
                        </div>
                    </div>
                </a>
            `;
        }));

        resultList.innerHTML = productHTMLArray.join("");
    } catch (error) {
        console.error("Error displaying items:", error);
        resultList.innerHTML = "<p>Failed to load products.</p>";
    }
}

function simpleFuzzyMatch(str, pattern) {
    pattern = pattern.toLowerCase().split('');
    str = str.toLowerCase();
    
    let patternIndex = 0;
    for (let i = 0; i < str.length; i++) {
        if (str[i] === pattern[patternIndex]) {
            patternIndex++;
            if (patternIndex === pattern.length) return true;
        }
    }
    return false;
}

function filterItems() {
    let query = document.getElementById("searchInput").value.toLowerCase();
    
    let filtered = products.filter(product => {
        let name = (product.Name || "").toLowerCase();
        let keyword = (product.Keyword || "").toLowerCase();
        let shopName = (product.ShopName || "").toLowerCase();
        
        return simpleFuzzyMatch(name, query) || 
               simpleFuzzyMatch(keyword, query) || 
               simpleFuzzyMatch(shopName, query);
    });
    
    displayItems(filtered);
    saveSearch(query);
}

// save the last 3 search
function saveSearch(query) {
    if (!query.trim()) return;

    let searches = JSON.parse(localStorage.getItem("recentSearches")) || [];
    searches = searches.filter(search => search !== query); 
    searches.unshift(query);

    if (searches.length > 3) searches.pop();

    localStorage.setItem("recentSearches", JSON.stringify(searches));
    displayRecentSearches();
}

// Load last search from localStorage
function loadLastSearch() {
    let searches = JSON.parse(localStorage.getItem("recentSearches")) || [];
    if (searches.length > 0) {
        document.getElementById("searchInput").value = searches[0];
        filterItems();
    }
    displayRecentSearches();
}

// Display recent search buttons
function displayRecentSearches() {
    let searches = JSON.parse(localStorage.getItem("recentSearches")) || [];
    let recentDiv = document.getElementById("recent-searches");
    recentDiv.innerHTML = "";

    searches.forEach(search => {
        let btn = document.createElement("button");
        btn.textContent = search;
        btn.onclick = () => {
            document.getElementById("searchInput").value = search;
            filterItems();
        };
        recentDiv.appendChild(btn);
    });
}

window.onload = loadCSV;
