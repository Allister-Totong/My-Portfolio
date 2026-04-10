function getArticlePicture(articleID, callback) {
    fetch(`api/articlepicture/?id=${articleID}`)
        .then(response => response.json())
        .then(data => {
            const result = `<img src="${data.imagePath}" alt="${data.alt}" class="${data.class}">`;
            callback(result);
        })
        .catch(error => {
            callback(`<p>${articleID}</p><img src="images/default.png" alt="No image available">`);
        });
}

function getAllArticles() {
    fetch('api/articles')
        .then(response => response.json())
        .then(fetchedArticles => { 
            articles = fetchedArticles;

            const articlePromises = articles.map(article => {
                return new Promise(resolve => {
                    getArticlePicture(article.ArticleID, function (articleImage) {
                        const articleHTML = `
                            <div class="quickArticle">
                                ${articleImage}
                                <h3>${article.Title}</h3>
                                <p>${article.Content}</p>
                            </div>
                        `;
                        resolve(articleHTML);
                    });
                });
            });

            Promise.all(articlePromises).then(allHTML => {
                document.getElementById("all-article-display").innerHTML = allHTML.join('');
            });
        })
        .catch(error => {
            console.error("Error fetching articles:", error);
        });
}
window.refreshArticles = getAllArticles;
document.addEventListener("DOMContentLoaded", () => {
    getAllArticles();
});
