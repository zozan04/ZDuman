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
        url: "ana_yemekler.html",
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
        url: "sulu_yemekler.html",
        dishes: [
            { name: "Tarator", id: "tarator" },
            { name: "Mercimek Çorbası", id: "mercimek-corba" },
            { name: "Sebze Çorbası", id: "sebze-corba" }
        ]
    },
    "karbonhidrat lezzetleri": {
        url: "kuru_yemekler.html",
        dishes: [
            { name: "Pilav Üstü Döner", id: "pilav-ustu-doner" },
            { name: "Simit Kebabı", id: "simit-kebabi" },
            { name: "Sultan Kebabı", id: "sultan-kebabi" }
        ]
    },
    "aperatifler": {
        url: "aperatifler.html",
        dishes: [
            { name: "Zeytinyağlı Enginar", id: "zeytinyagli-enginar" },
            { name: "Humus", id: "humus" },
            { name: "Bruschetta", id: "bruschetta" }
        ]
    },
    "tatlilar": {
        url: "tatlilar.html",
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

// Sepete yemek ekler
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('cart')) || []; // Sepeti al ya da yeni bir dizi oluştur
    const cartMessage = document.getElementById('cart-message');
    const cartItems = document.getElementById('cart-items');

    // Sepet boşsa mesajı göster
    if (cart.length === 0) {
        cartMessage.style.display = 'block';
        cartItems.style.display = 'none'; // Sepet öğelerini gizle
    } else {
        cartMessage.style.display = 'none'; // Mesajı gizle
        cartItems.style.display = 'block'; // Sepet öğelerini göster
        // Sepet öğelerini buraya ekleyebilirsiniz
        cart.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.textContent = `${item.name} - ${item.price}`;
            cartItems.appendChild(itemElement);
        });
    }
});



// silme butonu ekledi
document.addEventListener('DOMContentLoaded', function() {
    const cart = JSON.parse(localStorage.getItem('cart')) || []; // Sepeti al ya da yeni bir dizi oluştur
    const cartMessage = document.getElementById('cart-message');
    const cartItems = document.getElementById('cart-items');

    // Sepet boşsa mesajı göster
    if (cart.length === 0) {
        cartMessage.style.display = 'block';
        cartItems.style.display = 'none'; // Sepet öğelerini gizle
    } else {
        cartMessage.style.display = 'none'; // Mesajı gizle
        cartItems.style.display = 'block'; // Sepet öğelerini göster
        // Sepet öğelerini buraya ekleyebilirsiniz
        cart.forEach((item, index) => {
            const itemElement = document.createElement('div');
            itemElement.textContent = `${item.name} - ${item.price}`;
            itemElement.style.display = 'flex'; // Flex düzeni ile hizalamak için
            itemElement.style.justifyContent = 'space-between'; // Elemanları yan yana yerleştir
            itemElement.style.alignItems = 'center'; // Dikey hizalama

            const removeButton = document.createElement('button');
            removeButton.textContent = 'Sil';
            removeButton.classList.add('remove-button'); // Butona sınıf ekleyerek stil vermek için
            removeButton.onclick = function() {
                // Silme işlemi
                cart.splice(index, 1); // Yemeği sepetten kaldır
                localStorage.setItem('cart', JSON.stringify(cart)); // Sepeti güncelle
                itemElement.remove(); // DOM'dan öğeyi kaldır
                // Sepet boşsa mesajı tekrar göster
                if (cart.length === 0) {
                    cartMessage.style.display = 'block';
                    cartItems.style.display = 'none';
                }
            };

            itemElement.appendChild(removeButton); // Sil butonunu öğeye ekle
            cartItems.appendChild(itemElement); // Öğeyi sepete ekle
        });
    }
});
