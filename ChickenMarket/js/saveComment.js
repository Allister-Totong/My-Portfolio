document.getElementById("submitComment").addEventListener("click", function () {
    const userid = document.getElementById("userid").value.trim();
    const rating = document.getElementById("rating").value;
    const comment = document.getElementById("comment").value.trim();
    const productID = document.getElementById("productID").value;

    if (!rating || !comment) {
        alert("Fill in all fields first");
        return;
    }

    // Send data to PHP
    fetch("php/saveComment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            userid: userid,
            rating: rating,
            comment: comment,
            productID: productID
        })
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "Success") {

            document.getElementById("rating").value = "";
            document.getElementById("comment").value = "";

            setTimeout(() => getReview(productID), 100);
        } else {
            alert("Error saving comment: " + data);
        }
    })
    .catch(error => console.error("Error:", error));
});
