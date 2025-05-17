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


//profil işlemleri


