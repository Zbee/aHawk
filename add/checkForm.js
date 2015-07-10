function validateEmail(email) {
  var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
  return re.test(email);
}

function validateRealm(realm) {
  var re = /^[\w\-' ]+$/i;
  return re.test(realm);
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

  $("input[name='itemID']").keydown(function (e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1
      || (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) )
      || (e.keyCode >= 35 && e.keyCode <= 40)) {
        $("#itemIDAlert").html("")
        $("#itemIDGood").html("&#10003;")
        return
    }

    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
      e.preventDefault()
      $("#itemIDAlert").html("<div class='alert'>Only numbers here.</div>")
      $("#itemIDGood").html("&#120;")
      $("#submit").addClass("disabled")
      return
    }

    if ($(this).val().length >= 5 || $(this).val() > 99998) {
      e.preventDefault()
      $("#itemIDAlert").html(
        "<div class='alert'>Biggest item ID is 99998 as for 6.2."
        + "<br>So, the ID can't be more than that.</div>"
      )
      $("#itemIDGood").html("&#120;")
      $("#submit").addClass("disabled")
      return
    }

    $("#itemIDAlert").html("")
    $("#itemIDGood").html("&#120;")
    $("#submit").removeClass("disabled")
  })

  $("input[name='itemID']").keyup(function (e) {
    if ($("#itemIDAlert").html() == "") {
      $.ajax({
        type: "POST",
        url: "checkItem.php",
        data: {item: $(this).val()},
        dataType: "json",
        context: document.body,
        async: true,
        complete: function(res, stato) {
          if (res.responseJSON.actualItem == true) {
            $("#itemIDAlert").html("")
            $("#itemIDGood").html("&#10003;")
            $("#submit").removeClass("disabled")
            return
          } else {
            $("#itemIDAlert").html(
              "<div class='alert'>That is not a valid item ID in the US " 
              + "locale.<br>You can find a list of items on "
              + "<a href='http://www.wowhead.com/items'>Wowhead</a>.</div>"
            )
            $("#itemIDGood").html("&#120;")
            $("#submit").addClass("disabled")
            return
          }
        }
      })
    }
  })

  $("input[name='realm']").keyup(function (e) {
    if ($(this).val().length != 0 && !validateRealm($(this).val())) {
      $("#realmAlert").html(
        "<div class='alert'>Not a valid realm string.</div>"
      )
      $("#realmGood").html("&#120;")
      $("#submit").removeClass("disabled")
      return
    } else if ($(this).val().length == 0) {
      $("#realmAlert").html("")
      $("#realmGood").html("&#120;")
      $("#submit").removeClass("disabled")
      return
    }

    $.ajax({
      type: "POST",
      url: "checkRealm.php",
      data: {realm: $(this).val()},
      dataType: "json",
      context: document.body,
      async: true,
      complete: function(res, stato) {
        if (res.responseJSON.actualRealm == true) {
          $("#realmAlert").html("")
          $("#realmGood").html("&#10003;")
          $("#submit").removeClass("disabled")
          return
        } else {
          $("#realmAlert").html(
            "<div class='alert'>That is not a US realm.<br>You can find a "
            + "list of US realms <a href='https://battle.net/wow/status'>"
            + "here</a>.</div>"
          )
          $("#realmGood").html("&#120;")
          $("#submit").addClass("disabled")
          return
        }
      }
    })
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

    if ($("input[name='itemID']").val() == "") {
      $("#itemIDAlert").html(
        "<div class='alert'>This cannot be empty.</div>"
      )
      $("#itemIDGood").html("&#120;")
      bad = true
      bads[0] = true
    }

    if ($("input[name='itemID']").val().replace(/\D+/g, '')
      !== $("input[name='itemID']").val() && !bads[0]) {
      $("#itemIDAlert").html(
        "<div class='alert'>Only numbers here.</div>"
      )
      $("#itemIDGood").html("&#120;")
      bad = true
      bads[0] = true
    }

    if (!bads[0]) {
      $.ajax({
        type: "POST",
        url: "checkItem.php",
        data: {item: $("input[name='itemID']").val()},
        dataType: "json",
        context: document.body,
        async: true,
        complete: function(res, stato) {
          if (res.responseJSON.actualItem == true) {
            $("#itemIDAlert").html("")
            $("#itemIDGood").html("&#10003;")
            return
          } else {
            bad = true
            bads[0] = true
            $("#itemIDAlert").html(
              "<div class='alert'>That is not a valid item ID in the US " 
              + "locale.<br>You can find a list of items on "
              + "<a href='http://www.wowhead.com/items'>Wowhead</a>.</div>"
            )
            $("#itemIDGood").html("&#120;")
            $("#submit").addClass("disabled")
            return
          }
        }
      })
    }

    if ($("input[name='realm']").val() == "") {
      $("#realmAlert").html(
        "<div class='alert'>This cannot be empty</div>"
      )
      $("#realmGood").html("&#120;")
      bad = true
      bads[1] = true
    }

    if (validateRealm($("input[name='realm']")) && !bads[1]) {
      $("#realmAlert").html(
        "<div class='alert'>Not a valid realm string.</div>"
      )
      $("#realmGood").html("&#120;")
      bad = true
      bads[1] = true
    }

    if (!bads[1]) {
      $.ajax({
        type: "POST",
        url: "checkRealm.php",
        data: {realm: $("input[name='realm']").val()},
        dataType: "json",
        context: document.body,
        async: true,
        complete: function(res, stato) {
          if (res.responseJSON.actualRealm == true) {
            $("#realmAlert").html("")
            $("#realmGood").html("&#10003;")
            return
          } else {
            bad = true
            bads[1] = true
            $("#realmAlert").html(
              "<div class='alert'>That is not a US realm.<br>You can find a "
              + "list of US realms <a href='https://battle.net/wow/status'>"
              + "here</a>.</div>"
            )
            $("#realmGood").html("&#120;")
            return
          }
        }
      })
    }

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