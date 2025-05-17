
// Aydınlık ve Karanlık Mod Ayarı
document.getElementById("light").addEventListener("click", function() {
    const body = document.body;
    const themeIcon = document.getElementById("themeIcon");
    const circle = document.querySelector("#light .circle");

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

// Filtreleme kutusunu göster/gizle
function toggleFilterInput() {
    const filterText = document.querySelector(".filter-icon .filter-text");
    const filterBox = document.getElementById("filter-box"); // Filtre kutusunun ID'si

    // Filtre kutusunu göster veya gizle
    if (filterBox.style.display === "none" || filterBox.style.display === "") {
        filterBox.style.display = "block"; // Filtre kutusunu göster
        filterText.style.display = "none"; // "Filtrele" yazısını gizle
        document.getElementById("filterInput").focus(); // Giriş kutusuna odaklan
    } else {
        filterBox.style.display = "none"; // Filtre kutusunu gizle
        filterText.style.display = "inline"; // "Filtrele" yazısını göster
    }
}

// Yemekleri filtreleme işlevi
function filterDishes() {
    const nameInput = document.getElementById("filterInput").value.toLowerCase();
    const selectedCity = document.getElementById("selectedCity").value.toLowerCase();
    const dishItems = document.querySelectorAll(".dish-item");

    dishItems.forEach(item => {
        const title = item.querySelector(".dish-title").textContent.toLowerCase();
        const city = item.getAttribute("data-city")?.toLowerCase() || "";

        const matchName = title.includes(nameInput);
        const matchCity = selectedCity === "" || city === selectedCity;

        item.style.display = (matchName && matchCity) ? "block" : "none";
    });
}


function toggleDropdown() {
    const dropdown = document.querySelector(".dropdown-options");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

function selectCity(city, element) {
    // Şehri gizli input'a yaz
    document.getElementById("selectedCity").value = city;

    // Seçilen şehir adını "selected" alana yaz
    const selectedDiv = document.querySelector(".custom-dropdown .selected");
    selectedDiv.textContent = city === "" ? "Tüm Şehirler" : city;

    // Aktif seçilen li'yi belirle
    document.querySelectorAll(".dropdown-options li").forEach(li => {
        li.classList.remove("active");
    });
    element.classList.add("active");

    // Dropdown'ı kapat
    document.querySelector(".dropdown-options").style.display = "none";

    // Yemekleri filtrele
    filterDishes();
}

document.addEventListener('click', function(event) {
    const dropdown = document.querySelector(".custom-dropdown");
    const options = document.querySelector(".dropdown-options");

    if (!dropdown.contains(event.target)) {
        options.style.display = "none";
    }
});




// Sayfa yenilendiğinde varsayılan olarak "Filtrele" yazısını geri getir
window.addEventListener("load", () => {
    const filterText = document.querySelector(".filter-icon .filter-text");
    const filterBox = document.getElementById("filter-box");

    filterText.style.display = "inline";
    filterBox.style.display = "none"; // Filtre kutusunu başlangıçta gizle
});
document.addEventListener('click', function(event) {
    const filterBox = document.getElementById("filter-box");
    const filterIcon = document.querySelector(".filter-icon");

    if (!filterIcon.contains(event.target) && filterBox.style.display === "block") {
        filterBox.style.display = "none"; // Eğer kutunun dışında tıklanırsa kutuyu gizle
        document.querySelector(".filter-icon .filter-text").style.display = "inline"; // "Filtrele" yazısını göster
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


//Sepete ekleme işlemi
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
// çıkış yapma işlevi
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







