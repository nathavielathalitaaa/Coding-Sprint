/*
Template Name: Sinergi Hotel & Vila - HR Management System
Author: Sinergi Hotel & Vila
Version: 1.1.0
Website: https://sinergihv.com/
Contact: info@sinergihv.com
File: apps social friend init Js File
*/

var options = new List("friendList", {
    valueNames: [
        "friend_name",
        "username",
        "date",
        "status"
    ],
});

// sortble-dropdown
var sorttableDropdown = document.querySelectorAll('.sortble-dropdown');
if (sorttableDropdown) {
    sorttableDropdown.forEach(function (elem) {
        elem.querySelectorAll('.dropdown-menu .dropdown-item').forEach(function (item) {
            item.addEventListener('click', function () {
                var getHtml = item.innerHTML;
                elem.querySelector('.dropdown-title').innerHTML = getHtml;
            });
        });
    });
}