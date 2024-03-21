function notification(type, message) {
  if (type == "success") {
    toastr.success(message);
  } else if (type == "error") {
    toastr.error(message);
  }
}

function loader(type) {
  if (type) {
    $("#loaderModal").show();
  } else {
    $("#loaderModal").hide();
  }
}

function textEditor(id, value) {
  tinymce.init({
    selector: "#" + id,
    plugins:
      "print preview paste importcss searchreplace autolink directionality code visualblocks visualchars fullscreen image link media codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",
    menubar: "file edit view insert format tools table help",
    toolbar:
      "undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview print | insertfile image media link anchor codesample | ltr rtl",
    toolbar_sticky: true,
    image_advtab: true,
    content_css: "//www.tiny.cloud/css/codepen.min.css",
    importcss_append: true,
    height: 450,
    image_caption: false,
    quickbars_selection_toolbar:
      "bold italic | quicklink h2 h3 blockquote quickimage quicktable",
    noneditable_noneditable_class: "mceNonEditable",
    toolbar_mode: "sliding",
    contextmenu: "link image imagetools table",
    images_upload_url: SAVE_IMAGE,
    setup: function (editor) {
      editor.on("change", function () {
        editor.save();
      });
    },
    file_picker_types: "image",
    convert_urls: false,
  });
}

function customFormValidation(id) {
  $("#" + id).validate({
    errorElement: "span",
    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
      loader(true);
      if (id=='import-form') {
        importNotify();
      }
      return true;
    },
  });
}

function resetForm(id) {
  $("#" + id).trigger("reset");

  $("#" + id)
    .validate()
    .resetForm();
  $("#" + id)
    .find("*")
    .removeClass("is-invalid");
}

$.fn.modal.Constructor.prototype._enforceFocus = function() {};

function confirmStatusHttpModal(url, text) {
  swal.fire({
    title: "Confirmation",
    text: text,
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn btn-success mx-1",
    cancelButtonClass: "btn btn-danger mx-1",
    confirmButtonText: "Confirm",
    cancelButtonText: "Cancel",
    buttonsStyling: false,
  }).then(function (isConfirm) {
    if (isConfirm.value) {
        loader(true);
        location.href = url + "/" + isConfirm.value;
        return true;
    }
  });
  return false;
}

function rejectStatusHttpModal(url, text) {
  swal.fire({
    title: text,
    text: text,
    // input: "text",
    // type: "warning",

    html:
        '<select id="mySelect" class="form-control">' +
        '<option value="Customer Not interested">Customer Not interested</option>' +
        '<option value="Delay in delivery">Delay in delivery</option>' +
        '<option value="Erroneous Entry">Erroneous Entry</option>' +
        '<option value="Test order">Test order</option>' +
        '<option value="Out of stock">Out of stock</option>' +
        '<option value="Order Recreated">Order Recreated</option>' +
        '</select>',
    showCancelButton: true,
    // closeOnConfirm: false,
    confirmButtonClass: "btn btn-success mx-1",
    cancelButtonClass: "btn btn-danger mx-1",
    confirmButtonText: "Confirm",
    cancelButtonText: "Cancel",
    buttonsStyling: false,

    preConfirm: function() {
        var selectedValue = $('#mySelect').val();
        // Do something with the selected value
        console.log(selectedValue);

      },

  }).then((reason) => {

     var selectedValue = $('#mySelect').val();
        // Do something with the selected value
        console.log(selectedValue);

        if(selectedValue != ''){

          loader(true);
          location.href = url + "/" + selectedValue;
          return true;

        }else{

          alert("You need to write something!");

        }
    
  });


  return false;
}


function confirmStatusModal(id, status, datatable_id, msg) {
  var url = "";
  if (status == 0 || status == 1 || status == 2 || status == 3 || status == 4) {
    url = $("." + datatable_id + "_status").html();
    url = url.replace(/ID/g, id);
    url = url.replace(/STATUS/g, status);
  } else {
    return false;
  }

  var text = "Are you sure, you want to disable?";
  if (status == 1) {
    text = "Are you sure, you want to enable?";
  } else if (status == 2) {
    text = "Are you sure, you want to delete?";
  } else if (status == 3) {
    text = "Are you sure, you want to make offline?";
  } else if (status == 4) {
    text = "Are you sure, you want to make online?";
  }

  if (typeof msg === "undefined") {

  } else {
    text = msg;
  }

  Swal.fire({
    title: "Confirmation",
    text: text,
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn btn-success mx-1",
    cancelButtonClass: "btn btn-danger mx-1",
    confirmButtonText: "Confirm",
    cancelButtonText: "Cancel",
    buttonsStyling: false,
  }).then(function (isConfirm) {
    if (isConfirm.value) {
      loader(true);

      $.ajax({
        url: url,
        type: "GET",
        cache: false,
        success: function (data) {
          data = JSON.parse(data);
          if (data.response == "success") {
            notification("success", data.message, 5);
          } else {
            notification("error", data.message, 15);
          }

          $("#" + datatable_id)
            .DataTable()
            .draw(false);
          loader(false);
        },
        error: function (data) {
          errorHandler(data);
          loader(false);
        },
      });
    }
  });

  return false;
}

function errorHandler(data) {
  var html = [];

  if (data.responseJSON.errors !== undefined) {
    var errors = data.responseJSON.errors;
    var dot = false;
    if (Object.keys(errors).length > 1) {
      dot = true;
    }
    $.each(errors, function (index, value) {
      if (dot) {
        value = "&bull; " + value;
      }
      html.push(value);
    });
  } else {
    html.push("Something went wrong.");
  }

  notification("error", html.join("<br/>"));
}

function hideModel(id){
  $("#"+id).modal('hide');
}
