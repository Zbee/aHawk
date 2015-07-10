function validateEmail(email) {
  var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
  return re.test(email);
}

function validateString(string) {
  var re = /^[\w\-]+$/i;
  return re.test(string);
}

$(document).ready(function() {
  $("form").keydown(function(event) {
    if (event.which == 13) {
      event.preventDefault()
    }
  })

  $("input[name='subEmail'], input[name='subAPIEmail']").keyup(function (e) {
    if ($(this).val().length != 0 && !validateEmail($(this).val())) {
      $("#" + $(this).attr('name') + "Alert").html(
        "<div class='alert'>This is not a valid email address.</div>"
      )
      $("#" + $(this).attr('name') + "Good").html("&#120;")
      $("#submit").addClass("disabled")
      return
    } else if ($(this).val().length == 0) {
      $("#" + $(this).attr('name') + "Alert").html("")
      $("#" + $(this).attr('name') + "Good").html("&#120;")
      $("#submit").removeClass("disabled")
      return
    } else {
      $("#" + $(this).attr('name') + "Alert").html("")
      $("#" + $(this).attr('name') + "Good").html("&#10003;")
      $("#submit").removeClass("disabled")
      return
    }

    $("#" + $(this).attr('name') + "Alert").html("")
    $("#" + $(this).attr('name') + "Good").html("&#10003;")
    $("#submit").removeClass("disabled")
  })

  $("input[name='subIFTTTKey'], input[name='subIFTTTName']").keyup(function (e) {
    if ($(this).val().length != 0 && !validateString($(this).val())) {
      $("#" + $(this).attr('name') + "Alert").html(
        "<div class='alert'>This is not a valid string.</div>"
      )
      $("#" + $(this).attr('name') + "Good").html("&#120;")
      $("#submit").addClass("disabled")
      return
    } else if ($(this).val().length == 0) {
      $("#" + $(this).attr('name') + "Alert").html("")
      $("#" + $(this).attr('name') + "Good").html("&#120;")
      $("#submit").removeClass("disabled")
      return
    } else if ($(this).val().length > 25) {
      $("#" + $(this).attr('name') + "Alert").html(
        "<div class='alert'>This is too long.</div>"
      )
      $("#" + $(this).attr('name') + "Good").html("&#120;")
      $("#submit").addClass("disabled")
      return
    } else {
      $("#" + $(this).attr('name') + "Alert").html("")
      $("#" + $(this).attr('name') + "Good").html("&#10003;")
      $("#submit").removeClass("disabled")
      return
    }
  })

  $("input[name='itemID'], input[name='realm']").focus(function() {
    var focused = $(this).html()
    $("#itemIDAlert, #realmAlert").html('')
    $(this).html(focused)
  })

  $("#subEmail").click(function() {
    if (!$("#subEmail input[type='checkbox']").is(":checked"))
      $("#subEmailAlert, #subEmailGood, #subAlert, #subGood").html("")
  })

  $("#subIFTTT").click(function() {
    if (!$("#subIFTTT input[type='checkbox']").is(":checked"))
      $("#subIFTTTNameAlert, #subIFTTTNameGood").html("")
      $("#subIFTTTKeyAlert, #subIFTTTKeyGood, #subAlert, #subGood").html("")
  })

  $("#subAPI").click(function() {
    if (!$("#subAPI input[type='checkbox']").is(":checked"))
      $("#subAPIEmailAlert, #subAPIEmailGood, #subAlert, #subGood").html("")
  })

  $(".disabled").click(function(e) { e.preventDefault() })

  $("#submit input").click(function(e) {

    if ($(this).hasClass("disabled")) {
      e.preventDefault()
      return
    }

    var bad = false
    var bads = [false, false, [false, [false, false], false]]

    if (!$("#subEmail input[type='checkbox']").is(":checked")
      && !$("#subIFTTT input[type='checkbox']").is(":checked")
      && !$("#subAPI input[type='checkbox']").is(":checked")) {
      $("#subAlert").html(
        "<div class='alert'>You must subscribe in some way.</div>"
      )
      $("#subGood").html("&#120;")
      bad = true
      bads[2][0] = true
      bads[2][1] = true
      bads[2][2] = true
    }

    if ($("#subEmail input[type='checkbox']").is(":checked")
      && $("#subEmail input[name='subEmail']").val() == "") {
      $("#subEmailAlert").html(
        "<div class='alert'>This cannot be empty.</div>"
      )
      $("#subEmailGood").html("&#120;")
      bad = true
      bads[2][0] = true
    }

    if (validateEmail($("input[name='subEmail']")) && !bads[2][0]) {
      $("#subEmailAlert").html(
        "<div class='alert'>This is not a valid email address.</div>"
      )
      $("#subEmailGood").html("&#120;")
      bad = true
      bads[2][0] = true
    }

    if ($("#subIFTTT input[type='checkbox']").is(":checked")
      && $("input[name='subIFTTTName']").val() == "") {
      $("#subIFTTTNameAlert").html(
        "<div class='alert'>This cannot be empty.</div>"
      )
      $("#subIFTTTNameGood").html("&#120;")
      bad = true
      bads[2][1][0] = true
    }

    if (validateString($("input[name='subIFTTTName']")) && !bads[2][1][0]) {
      $("#subIFTTTNameAlert").html(
        "<div class='alert'>This is not a valid string.</div>"
      )
      $("#subIFTTTNameGood").html("&#120;")
      bad = true
      bads[2][1][0] = true
    }

    if ($("#subIFTTT input[type='checkbox']").is(":checked")
      && $("input[name='subIFTTTKey']").val() == "") {
      $("#subIFTTTKeyAlert").html(
        "<div class='alert'>This cannot be empty.</div>"
      )
      $("#subIFTTTKeyGood").html("&#120;")
      bad = true
      bads[2][1][1] = true
    }

    if (validateString($("input[name='subIFTTTKey']")) && !bads[2][1][1]) {
      $("#subIFTTTKeyAlert").html(
        "<div class='alert'>This is not a valid string.</div>"
      )
      $("#subIFTTTKeyGood").html("&#120;")
      bad = true
      bads[2][1][1] = true
    }

    if ($("#subAPI input[type='checkbox']").is(":checked")
      && $("input[name='subAPIEmail']").val() == "") {
      $("#subAPIEmailAlert").html(
        "<div class='alert'>This cannot be empty.</div>"
      )
      $("#subAPIEmailGood").html("&#120;")
      bad = true
      bads[2][2] = true
    }

    if (validateEmail($("input[name='subAPIEmail']")) && !bads[2][2]) {
      $("#subAPIEmailAlert").html(
        "<div class='alert'>This is not a valid email address.</div>"
      )
      $("#subAPIEmailGood").html("&#120;")
      bad = true
      bads[2][2] = true
    }

    if (bad) {
      e.preventDefault()
      $("#submit").addClass("disabled")
      return
    }
  })
})