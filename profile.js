//Sepete ekleme işlemi
document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", function () {
        let mealId = this.getAttribute("data-dish-id");

        fetch("sepetim.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "meal_id=" + mealId
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Kullanıcıya geri bildirim ver
        })
        .catch(error => console.error("Hata:", error));
    });
});



// Favori ikonlarına tıklandığında renk değiştirme işlevi
document.querySelectorAll('.favorite-icon').forEach(icon => {
    icon.addEventListener('click', function() {
        // Tıklandığında aktif hale getirme (veya kaldırma)
        this.classList.toggle('active');
    });
});


// Favori ekleme işlevi
function addToFavorites(mealId) {
    fetch('favoriler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'mealId=' + mealId
    })
    .then(response => response.json())
    
    .catch(error => {
        console.error('Error:', error);
    });
}



// Sayfanın herhangi bir yerine tıklanınca menüyü kapatma
document.addEventListener("click", function (event) {
    var menu = document.getElementById("logout-menu");
    var icon = document.querySelector(".login-icon");
    
    if (!icon.contains(event.target) && !menu.contains(event.target)) {
        menu.style.display = "none";
    }
});
