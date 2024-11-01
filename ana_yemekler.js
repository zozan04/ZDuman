
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
    const filterInput = document.getElementById("filterInput").value.toLowerCase(); // Filtreleme girişini al
    const dishItems = document.querySelectorAll(".dish-item"); // Tüm yemek öğelerini al

    dishItems.forEach(item => {
        const title = item.querySelector(".dish-title").textContent.toLowerCase(); // Başlığı al
        // Başlık, filtreleme girişine göre eşleşiyorsa göster, aksi takdirde gizle
        item.style.display = title.includes(filterInput) ? "block" : "none";
    });
}

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

// Arama kutusunu açıp kapatma
function toggleSearchInput() {
    const searchBox = document.getElementById("search-box");
    searchBox.style.display = searchBox.style.display === "block" ? "none" : "block";
    document.getElementById("searchInput").focus();
}

function searchDishes() {
    const searchInput = document.getElementById("searchInput").value.toLowerCase();
    const dishItems = document.querySelectorAll(".dish-item");
    let foundAny = false;

    // Aynı sayfadaki öğeleri kontrol etme
    dishItems.forEach(item => {
        const title = item.querySelector(".dish-title").textContent.toLowerCase();
        const username = item.querySelector(".user-name").textContent.toLowerCase();
        const category = item.querySelector(".dish-category").textContent.toLowerCase();

        if (title.includes(searchInput) || username.includes(searchInput) || category.includes(searchInput)) {
            item.style.display = "block";
            foundAny = true;
        } else {
            item.style.display = "none";
        }
    });

    // Eğer bulunamadıysa diğer sayfalara yönlendirme
    if (!foundAny && searchInput) {
        navigateToOtherPages(searchInput);
    } else {
        document.getElementById("noResultsMessage").style.display = foundAny ? "none" : "block";
    }
}

function navigateToOtherPages(searchTerm) {
    // Ana yemek sayfasına yönlendirme örneği
    if (window.location.href.indexOf("ana_yemekler") === -1) {
        window.location.href = `/ana_yemekler.html?search=${searchTerm}`;
    }
    // Diğer yemek kategorileri ve sayfalar için de benzer kontroller
}

// Sayfa yüklendiğinde URL'den arama terimini çekip arama yap
window.addEventListener("load", () => {
    const params = new URLSearchParams(window.location.search);
    const searchTerm = params.get("search");
    if (searchTerm) {
        document.getElementById("searchInput").value = searchTerm;
        searchDishes();
    }
});








