jQuery(document).ready(function ($) {
  $(".variations_form").on("click", ".upow-swatch-item", function (e) {
    // clicked swatch
    const el = $(this);
    const select = el.closest(".value").find("select");
    const value = el.data("value");
    const colorName = el.data("title");

    el.addClass("selected").siblings(".selected").removeClass("selected");
    select.val(value);
    select.change();

    const label = el.closest("tr").find('label[for="pa_color"]');

    if (label.find(".upow-selected-color-name").length) {
      label.find(".upow-selected-color-name").text(" " + colorName);
    } else {
      label.append(
        '<span class="upow-selected-color-name"> ' + colorName + "</span>"
      );
    }
  });

  jQuery(document).ready(function ($) {

    // Store the original image source on page load.
    $(".wp-post-image").each(function () {
      var product_image = $(this);
      product_image.data("original-src", product_image.attr("src"));
      product_image.data("original-srcset", product_image.attr("srcset"));
    });

    $(".variations_form").on("change", "select", function () {
      var form = $(this).closest(".variations_form");
      var product_image = form.closest(".product").find(".wp-post-image");

      form.trigger("check_variations");

      // Get variation image URL
      form.on("found_variation", function (event, variation) {
        if (variation && variation.image && variation.image.src) {
          product_image.attr("src", variation.image.src);
          product_image.attr("srcset", variation.image.srcset);
        }
      });
    });

    $(".variations_form").on("change", "select[name^='attribute_pa_']", function () {
      var form = $(this).closest(".variations_form");
      var selectedAttribute = $(this).attr("name");
      var selectedValue = $(this).val();
      var relatedAttribute = selectedAttribute === "attribute_pa_size" ? "attribute_pa_color" : "attribute_pa_size";
      var swatchWrapper = form.find(".upow-swatch-wrapper[data-attribute_name='" + relatedAttribute + "']");
      var availableVariations = form.data("product_variations");
  
      // Clear previous states
      swatchWrapper.find(".upow-swatch-item").removeClass("enabled").addClass("disabled");
  
      if (selectedValue) {
          updateSwatches(swatchWrapper, availableVariations, selectedAttribute, selectedValue, relatedAttribute);
      } else {
          // If no value is selected, enable all swatches
          swatchWrapper.find(".upow-swatch-item").removeClass("disabled").addClass("enabled");
      }
  
      synchronizeSwatchesWithDropdown(relatedAttribute);
  });
  
  // Function to update swatches based on available variations
  function updateSwatches(swatchWrapper, availableVariations, selectedAttribute, selectedValue, relatedAttribute) {
      $.each(availableVariations, function (index, variation) {
          if (variation.attributes[selectedAttribute] === selectedValue) {
              var relatedValue = variation.attributes[relatedAttribute];
              var matchingSwatch = swatchWrapper.find(".upow-swatch-item[data-value='" + relatedValue + "']");
              if (matchingSwatch.length) {
                  matchingSwatch.removeClass("disabled").addClass("enabled");
              }
          }
      });
  }
  
  // Function to synchronize swatches with dropdown options
  function synchronizeSwatchesWithDropdown(attributeName) {
      var dropdown = $("select[name='" + attributeName + "']");
      var swatchWrapper = $(".upow-swatch-wrapper[data-attribute_name='" + attributeName + "']");
  
      var enabledOptions = dropdown.find("option:not(:first)").filter(function () {
          return !$(this).is(":disabled");
      }).map(function () {
          return $(this).val();
      }).get();
  
      // Enable swatches matching dropdown options
      swatchWrapper.find(".upow-swatch-item").each(function () {
          var swatch = $(this);
          if (enabledOptions.includes(swatch.data("value"))) {
              swatch.removeClass("disabled").addClass("enabled");
          } else {
              swatch.removeClass("enabled").addClass("disabled");
          }
      });
  }
  


    // Handle the Clear button click to reset the image.
    $(".reset_variations").on("click", function (e) {
      e.preventDefault();

      var form = $(this).closest(".variations_form");
      var product_image = form.closest(".product").find(".wp-post-image");

      // Reset the image to the original source
      var original_image_src = product_image.data("original-src");

      if (original_image_src) {
        product_image.attr("src", original_image_src);
      }
      if (original_image_src) {
        product_image.attr("srcset", original_image_src);
      }

      // Clear selected options
      form.find("select").val("");
      // Reset swatches
      form.trigger("reset_data");
    });
  });



 
























});


jQuery(document).ready(function ($) {
  $(".reset_variations").on("click", function (e) {
    e.preventDefault(); 
    $(".upow-swatch-item").removeClass("selected");
    $(".variations_form").trigger("reset_variations");
  });
});
