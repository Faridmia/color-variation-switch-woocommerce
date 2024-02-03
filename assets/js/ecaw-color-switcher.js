jQuery(document).ready(function ($) {
  $(".variations_form").on("click", ".swatch", function (e) {
    // clicked swatch
    const el = $(this);
    // original select dropdown with variations
    const select = el.closest(".value").find("select");
    // this specific term slug, i.e color slugs, like "coral", "grey" etc
    const value = el.data("value");

    // do three things
    el.addClass("selected").siblings(".selected").removeClass("selected");
    select.val(value);
    select.change();
  });
});

