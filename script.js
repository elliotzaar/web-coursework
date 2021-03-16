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

Array.from(document.getElementsByClassName("role-switch-perms")).forEach(function(e) {
  e.addEventListener('click', function() {
    processRoleSwitchPerm(e.id.split('-')[1], e.id.split('-')[4], e.checked);
  });
});
