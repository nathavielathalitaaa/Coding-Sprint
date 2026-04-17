/*
Template Name: Sinergi Hotel & Vila - HR Management System
Author: Sinergi Hotel & Vila
Website: https://sinergihv.com/
Contact: info@sinergihv.com
File: Form editor balloon Js File
*/

var ckClassicEditor = document.querySelectorAll(".ckeditor-balloon")
if (ckClassicEditor) {
    Array.from(ckClassicEditor).forEach(function () {
        BalloonEditor
            .create(document.querySelector('.ckeditor-balloon'))
            .catch(function (error) {
                console.error(error);
            });
    });
}