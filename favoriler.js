// Silme işlevi
function deleteMeal(mealId) {
    fetch('favoriler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'deleteMealId=' + mealId
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
           
            location.reload(); // Sayfayı yenile
       
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

