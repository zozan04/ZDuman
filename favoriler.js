// Tema değiştirme butonuna tıklama olayını dinleyin
document.getElementById("light").addEventListener("click", function() {
    const body = document.body;
    const themeIcon = document.getElementById("themeIcon");
    const circle = document.querySelector(".light.circle");

    if (body.classList.contains("light-mode")) {
        // Karanlık moda geçiş
        body.classList.remove("light-mode");
        body.classList.add("dark-mode");
        themeIcon.classList.remove("fa-sun");
        themeIcon.classList.add("fa-moon");

        // Çemberin içini siyah yap, ay ikonunu beyaz yap
        circle.style.backgroundColor = "black";
        themeIcon.style.color = "white";
    } else {
        // Aydınlık moda geçiş
        body.classList.remove("dark-mode");
        body.classList.add("light-mode");
        themeIcon.classList.remove("fa-moon");
        themeIcon.classList.add("fa-sun");

        // Çemberin içini beyaz yap, güneş ikonunu siyah yap
        circle.style.backgroundColor = "white";
        themeIcon.style.color = "black";
    }
});
// Arama kutusunu açıp kapatma işlevi
function toggleSearchInput() {
    const searchBox = document.getElementById("search-box");
    searchBox.style.display = searchBox.style.display === "block" ? "none" : "block";
    if (searchBox.style.display === "block") {
        document.getElementById("searchInput").focus(); // Arama kutusuna odaklanma
    }
}

// Enter tuşuna basıldığında arama ve yönlendirme işlevi
document.getElementById("searchInput").addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        searchAndRedirect(event.target.value.toLowerCase());
    }
});

// Kategorilerin bulunduğu sayfanın URL'si
const categories = {
    "ana yemekler": {
        url: "ana_yemekler.php",
        dishes: [
            { name: "Ciğer Yahnisi", id: "ciger-yahnisi" },
            { name: "Baharatlı Piliç Külbastı", id: "baharatli-pilic-kulbasti" },
            { name: "Bonfile Kavurma", id: "bonfile-kavurma" },
            { name: "Köri Soslu Tavuk", id: "kori-soslu-tavuk" },
            { name: "Çökertme Kebabı", id: "cokertme-kebabi" },
            { name: "Beş Kardeş Yahnisi", id: "bes-kardes-yahnisi" },
            { name: "Kuzu Etli Enginar Yahni", id: "kuzu-etli-enginar-yahni" },
            { name: "Sakız Yahnisi", id: "sakiz-yahnisi" },
            { name: "Karnabahar Kavurması", id: "karnabahar-kavurmasi" },
            { name: "Kişniş Soslu Somon Balığı", id: "Kisnis-Soslu-Somon-Baligi" },
        ]
    },
    "sulu yemekler": {
        url: "sulu_yemekler.php",
        dishes: [
            { name: "Tarator", id: "tarator" },
            { name: "Mercimek Çorbası", id: "mercimek-corba" },
            { name: "Sebze Çorbası", id: "sebze-corba" }
        ]
    },
    "karbonhidrat lezzetleri": {
        url: "karbonhidrat_lezzetleri.php",
        dishes: [
            { name: "Pilav Üstü Döner", id: "pilav-ustu-doner" },
            { name: "Simit Kebabı", id: "simit-kebabi" },
            { name: "Sultan Kebabı", id: "sultan-kebabi" }
        ]
    },
    "aperatifler": {
        url: "aperatifler.php",
        dishes: [
            { name: "Zeytinyağlı Enginar", id: "zeytinyagli-enginar" },
            { name: "Humus", id: "humus" },
            { name: "Bruschetta", id: "bruschetta" }
        ]
    },
    "tatlı çeşitleri": {
        url: "tatli_cesitleri.php",
        dishes: [
            { name: "Cheesecake", id: "cheesecake" },
            { name: "Baklava", id: "baklava" },
            { name: "Tiramisu", id: "tiramisu" }
        ]
    }
};

// Arama fonksiyonu
function searchAndRedirect(query) {
    // Kategori ve yemekleri kontrol et
    for (const category in categories) {
        if (category.includes(query)) {
            window.location.href = categories[category].url; // Kategori sayfasına yönlendir
            return;
        }
        const foundDish = categories[category].dishes.find(dish => dish.name.toLowerCase() === query);
        if (foundDish) {
            window.location.href = `${categories[category].url}#${foundDish.id}`; // Yemek ID'si ile yönlendirme
            return;
        }
    }
    alert("Aradığınız yemek veya kategori bulunamadı.");
}


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

//çıkış
function toggleLogoutMenu() {
    var menu = document.getElementById("logout-menu");
    if (menu.style.display === "none" || menu.style.display === "") {
        menu.style.display = "block";
    } else {
        menu.style.display = "none";
    }
}

// Sayfanın herhangi bir yerine tıklanınca menüyü kapatma
document.addEventListener("click", function (event) {
    var menu = document.getElementById("logout-menu");
    var icon = document.querySelector(".login-icon");
    
    if (!icon.contains(event.target) && !menu.contains(event.target)) {
        menu.style.display = "none";
    }
});



document.querySelectorAll(".add-to-cart").forEach(button => {
    button.addEventListener("click", function () {
        let mealId = this.getAttribute("data-dish-id");
        let mealCard = this.closest(".dish-item"); // Yemek kartını bul

        fetch("sepetim.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "meal_id=" + mealId
        })
        .then(response => response.json())
        .then(data => {
            // Eğer bildirim zaten varsa, tekrar ekleme
            if (mealCard.querySelector(".cart-notification")) return;

            // Bildirim oluştur
            let notification = document.createElement("div");
            notification.innerText = "Sepete eklendi";
            notification.classList.add("cart-notification");

            // Yemek kartına ekle
            mealCard.appendChild(notification);

            // 1 saniye sonra kaybolsun
            setTimeout(() => {
                notification.remove();
            }, 1000);
        })
        .catch(error => console.error("Hata:", error));
    });
});

// Modal'ı açma fonksiyonu
function openModal(dishId, dishName, dishImage, dishDescription, dishPrice) {
    var modal = document.getElementById("myModal");
    var modalImg = document.getElementById("modal-img");
    var modalTitle = document.getElementById("modal-title");
    var modalDescription = document.getElementById("modal-content");
    var modalPrice = document.getElementById("modal-price").getElementsByTagName("span")[0];
    var addToCartBtn = document.getElementById("add-to-cart-modal");

    // Modal içeriğini güncelle
    modalImg.src = dishImage;
    modalTitle.textContent = dishName;  // Yalnızca modal içindeki yemek adı değişir
    modalDescription.textContent = dishDescription;
    modalPrice.textContent = dishPrice;
    addToCartBtn.setAttribute('data-dish-id', dishId);
    addToCartBtn.setAttribute('data-dish-name', dishName);
    addToCartBtn.setAttribute('data-dish-price', dishPrice);

    modal.style.display = "block"; // Modal'ı aç
}

// Modal'ı kapatma fonksiyonu
var closeModal = document.getElementsByClassName("close")[0];
closeModal.onclick = function() {
    document.getElementById("myModal").style.display = "none";
}

// Sayfa dışında tıklanırsa modal'ı kapatma
window.onclick = function(event) {
    if (event.target == document.getElementById("myModal")) {
        document.getElementById("myModal").style.display = "none";
    }
}

// Modal içindeki Sepete Ekle butonuna tıklanınca bildirimi göster
document.getElementById("add-to-cart-modal").onclick = function () {
    // Modal içindeki bildirim
    var notification = document.getElementById("notification");
    
    // Bildirimi göster
    notification.style.display = "block";

    // 2 saniye sonra bildirimi gizle
    setTimeout(function () {
        notification.style.display = "none";
    }, 2000);
}
//modalda Sepete ekleme işlemi
document.querySelectorAll(".add-to-carts").forEach(button => {
    button.addEventListener("click", function () {
        let mealId = this.getAttribute("data-dish-id");
        let mealCard = this.closest(".dish-item"); // Yemek kartını bul

        fetch("sepetim.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "meal_id=" + mealId
        })
        .then(response => response.json())
        .then(data => {
            // Eğer bildirim zaten varsa, tekrar ekleme
            if (mealCard.querySelector(".cart-notification")) return;

            // Bildirim oluştur
            let notification = document.createElement("div");
            notification.innerText = "Sepete eklendi";
            notification.classList.add("cart-notification");

            // Yemek kartına ekle
            mealCard.appendChild(notification);

            // 1 saniye sonra kaybolsun
            setTimeout(() => {
                notification.remove();
            }, 1000);
        })
        .catch(error => console.error("Hata:", error));
    });
});