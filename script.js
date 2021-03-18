var HttpClient = function() {
    this.get = function(url, callback) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function() {
            if(httpRequest.readyState == 4) {
              callback(httpRequest.status, httpRequest.responseText);
            }
        }

        httpRequest.open("GET", url, true);
        httpRequest.send(null);
    }
}

function processRoleSwitchPerm(role_id, perm_id, status) {
  var client = new HttpClient();
  client.get('webhook.php?change-role-perm-status&role=' + role_id + '&perm=' + perm_id + '&status=' + status, function(status, response) {
      if(status != 200) {
        alert(response);
      }
  });
}

function processRoleControlChange(user_id, role_id) {
  var client = new HttpClient();
  client.get('webhook.php?change-user-role&user=' + user_id + '&role=' + role_id, function(status, response) {
      if(status != 200) {
        alert(response);
      }
  });
}

Array.from(document.getElementsByClassName("role-switch-perms")).forEach(function(e) {
  e.addEventListener('click', function() {
    processRoleSwitchPerm(e.id.split('-')[1], e.id.split('-')[4], e.checked);
  });
});

var usrinfo_role_selector = document.getElementById("usrinfo-role-selector");
if(typeof(usrinfo_role_selector) != 'undefined' && usrinfo_role_selector != null){
  usrinfo_role_selector.addEventListener('change', function() {
    processRoleControlChange(this.getAttribute('data-userid'), this.value);
  });
}

var newaccount_balance_checkbox = document.getElementById("new-accnt-balance-checkbox");
if(typeof(newaccount_balance_checkbox) != 'undefined' && newaccount_balance_checkbox != null){
  newaccount_balance_checkbox.addEventListener('change', function() {
    document.getElementById("new-accnt-balance").hidden = !this.checked;
    document.getElementById("new-accnt-balance").disabled = !this.checked;
  });

  document.getElementById("new-accnt-balance").addEventListener('focusout', function() {
    if(this.value == "") {
      this.value = "0.00";
      document.getElementById("new-accnt-balance-lbl").classList.add("is-dirty");
    }
  });
}
