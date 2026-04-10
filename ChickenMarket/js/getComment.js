let currentUserId=1//temporary set to have user of website as user 1 a.k.a. admin.

function getReview(productID) {
    fetch('api/comment')
        .then(response => response.json())
        .then(comments => {
            const filteredComments = comments.filter(comment => Number(comment.ProductID) === Number(productID));
            const reviewsContainer = document.getElementById("all-review-display");

            if (filteredComments.length === 0) {
                reviewsContainer.innerHTML = "<p>No comments found for this product.</p>";
                return;
            }

            let reviewHTML = filteredComments.map(row => {
                // Check if current user is the comment owner. This is to ensure that editing and deletion of
                // comments can only be done by the user that owns that comment. This is done by getting the 
                // comment's creator id, then have it checked with the current ID that is logged in.
                const isOwner = checkCommentOwner(row.UserID); 
                
                let editDeleteButtons = '';
                if (isOwner) {
                    editDeleteButtons = `
                        <div class="comment-actions">
                            <button class="edit-comment" data-commentid="${row.CommentID}">Edit</button>
                            <button class="delete-comment" data-commentid="${row.CommentID}">Delete</button>
                        </div>
                    `;
                }

                return `
                    <div class='review' data-commentid="${row.CommentID}">
                        <h3><i class='fa-solid fa-circle-user'></i> ${row.Name}</h3>
                        <p>Rating: ${row.Rating} <i class='fa-solid fa-star'></i></p>
                        <p class="comment-text">${row.Comment}</p>
                        ${editDeleteButtons}
                    </div>
                `;
            }).join('');

            reviewsContainer.innerHTML = reviewHTML;

            // Add event listeners for edit and delete buttons
            document.querySelectorAll('.edit-comment').forEach(button => {
                button.addEventListener('click', editComment);
            });

            document.querySelectorAll('.delete-comment').forEach(button => {
                button.addEventListener('click', deleteComment);
            });
        })
        .catch(error => {
            console.error('Error fetching comments:', error);
            document.getElementById("all-review-display").innerHTML = "<p>Failed to load comments.</p>";
        });
}

function editComment(event) {
    const commentId = event.target.dataset.commentid;
    const reviewElement = event.target.closest('.review');
    const commentText = reviewElement.querySelector('.comment-text').textContent;
    
    // Replace text with editable textarea
    reviewElement.querySelector('.comment-text').innerHTML = `
        <textarea class="edit-comment-text">${commentText}</textarea>
        <button class="save-comment" data-commentid="${commentId}">Save</button>
        <button class="cancel-edit">Cancel</button>
    `;
    
    // Add event listeners for save and cancel
    reviewElement.querySelector('.save-comment').addEventListener('click', saveEditedComment);
    reviewElement.querySelector('.cancel-edit').addEventListener('click', () => {
        getReview(getCurrentProductId()); // Reload comments
    });
}

function saveEditedComment(event) {
    const commentId = event.target.dataset.commentid;
    const newText = event.target.closest('.review').querySelector('.edit-comment-text').value;
    
    fetch(`api/comment.php?action=edit&id=${commentId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ comment: newText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            getReview(getCurrentProductId()); // Reload comments
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteComment(event) {
    if (!confirm('Are you sure you want to delete this comment?')) return;
    
    const commentId = event.target.dataset.commentid;
    
    fetch(`api/comment.php?action=delete&id=${commentId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            getReview(getCurrentProductId()); // Reload comments
        }
    })
    .catch(error => console.error('Error:', error));
}

// Helper function to get current product ID from URL
function getCurrentProductId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// You need to implement this based on your authentication system
function checkCommentOwner(userId) {
    if(userId==currentUserId){
        return true;
    }else{
        return false;
    }
    
}

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productID = urlParams.get('id');
    if (productID) {
        getReview(productID);
    }
});