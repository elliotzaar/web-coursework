<?php
include_once('classes/page-builder.php');
include_once('classes/access-rules.php');
include_once('classes/log.php');
include_once('classes/account.php');

$usr_perms = AccessRules::getUsersPermissions($_COOKIE['uid']);

if(!AccessRules::hasPermission('VIEW_ACCOUNTS', $usr_perms)) {
  NoPermissionPage::display('Рахунки');
  die();
}

$page = new Page('Рахунки');

$content = '';

if(isset($_GET['create'])) {
  if(!AccessRules::hasPermission('CREATE_ACCOUNTS', $usr_perms)) {
    NoPermissionPage::display('Рахунки');
    die();
  }

  if(isset($_GET['success'])) {
    $page->setContent('Рахунок успішно створено.<div class="carditem-border-top"><button class="mdl-button mdl-js-button mdl-button--accent" onclick="location.href=\'accounts.php\'">Перейти до пошуку рахунків</button></div>');
    $page->create();
    die();
  }

  if(isset($_POST['newaccname']) && isset($_POST['newaccnum']) && isset($_POST['newacccurr'])) {
    $accbalance = 0.00;

    if(isset($_POST['newaccbalance'])) {
      if(preg_match('/^\d{1,18}(,\d{3})*(\.\d\d)?$/', $_POST['newaccbalance'])) {
        $accbalance = floatval($_POST['newaccbalance']);
      } else {
        header("Location: ./accounts.php?create&invalidb");
        die();
      }
    }

    if(Accounts::accountNumberExists($_POST['newaccnum'])) {
      header("Location: ./accounts.php?create&invalidn");
      die();
    }

    if(!Currency::currencyExists($_POST['newacccurr'])) {
      header("Location: ./accounts.php?create&invalidc");
      die();
    }

    $nacc_id = Accounts::createAccount($_POST['newaccname'], $_POST['newaccnum'], $accbalance, $_POST['newacccurr']);
    UsersLog::record($_COOKIE['sid'], UsersLog::CREATE_ACCOUNT, 'Створено рахунок id'.$nacc_id.' (num '.$_POST['newaccnum'].', name '.$_POST['newaccname'].', bal '.$accbalance.':'.$_POST['newacccurr'].')');

    header("Location: ./accounts.php?create&success");
    die();
  }

  $content .= '<h2 class="mdl-card__title-text">Новий рахунок</h2>';
  $content .= '<div class="mdl-card__supporting-text">';

  $content .= '<form action="accounts.php?create" method="post">
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="newaccname" id="new-accnt-name">
    <label class="mdl-textfield__label" for="new-accnt-name">Найменування</label>
  </div>
  <br />
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalidn']) ? ' is-invalid' : '').'">
    <input class="mdl-textfield__input" type="text" name="newaccnum" id="new-accnt-number" pattern="-?[A-Z0-9]*(\.[A-Z0-9]+)?">
    <label class="mdl-textfield__label" for="new-accnt-number">Номер</label>
  </div>
  <br />

  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label'.(isset($_GET['invalidb']) ? ' is-invalid' : '').'" id="new-accnt-balance-lbl">
    <label class="mdl-textfield__label" for="new-accnt-balance">Початковий баланс</label>
    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="new-accnt-balance-checkbox">
      <input type="checkbox" id="new-accnt-balance-checkbox" class="mdl-checkbox__input">
      <span class="mdl-checkbox__label">Є початковий баланс</span>
    </label>
    <input class="mdl-textfield__input" type="text" name="newaccbalance" id="new-accnt-balance" hidden disabled value="0.00" pattern="^\d{1,18}(,\d{3})*(\.\d\d)?$">
  </div>
  <br />
  <select id="new-accnt-currency-selector" class="mdl-textfield__input" name="newacccurr">
  <option value="0">Валюта</option>';
$currency_list = Currency::getCurrenciesList();
foreach($currency_list as $r) {
  $content .= '<option value='.$r['id'].''.((isset($_GET['acccurr']) && $r['id'] == $_GET['acccurr']) ? ' selected' : '').'>'.$r['code'].' - '.$r['name'].'</option>';
}
$content .= '</select>';
$content .= '<br /><button id="new-accnt-btn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Створити</button>';
  $content .= '</form>';
  $content .= '</div>';

  $page->setContent($content);
  $page->create();
  die();
}

if(AccessRules::hasPermission('CREATE_ACCOUNTS', $usr_perms)) {
  $content .= '<div class="carditem-border-bottom"><button onclick="location.href=\'accounts.php?create\'" class="mdl-button mdl-js-button mdl-button--primary mdl-js-ripple-effect btn-align-right">Створити рахунок</button></div>';
}

$content .= '<h2'.(AccessRules::hasPermission('CREATE_ACCOUNTS', $usr_perms) ? ' style="margin-top: 8px"' : '').' class="mdl-card__title-text">Пошук рахунків</h2>';
$content .= '<div class="mdl-card__supporting-text">';

$content .= '<form action="accounts.php" method="get">
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="accname" id="accnt-search-name" value="'.(isset($_GET['accname']) ? $_GET['accname'] : '').'">
    <label class="mdl-textfield__label" for="accnt-search-name">Найменування</label>
  </div>
  <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <input class="mdl-textfield__input" type="text" name="accnum" id="accnt-search-number" pattern="-?[A-Z0-9]*(\.[A-Z0-9]+)?" value="'.(isset($_GET['accnum']) ? $_GET['accnum'] : '').'">
    <label class="mdl-textfield__label" for="accnt-search-number">Номер</label>
  </div>
  <select id="accnt-search-currency-selector" class="mdl-textfield__input" name="acccurr">
  <option value="0">Валюта</option>';
$currency_list = Currency::getCurrenciesList();
foreach($currency_list as $r) {
  $content .= '<option value='.$r['id'].''.((isset($_GET['acccurr']) && $r['id'] == $_GET['acccurr']) ? ' selected' : '').'>'.$r['code'].' - '.$r['name'].'</option>';
}
$content .= '</select>';
$content .= '<br /><button id="accnt-search-btn" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" type="submit">Виконати пошук</button>';
$content .= '</form></div>';

if(isset($_GET['accname']) && isset($_GET['accnum']) && isset($_GET['acccurr']) && ($_GET['accname'] != '' || $_GET['accnum'] != '' || $_GET['acccurr'] != 0)) {
  $content .= '<table class="mdl-data-table page-table">
  <thead>
  <tr>
  <th class="mdl-data-table__cell--non-numeric">Найменування</th>
  <th class="mdl-data-table__cell">Номер</th>
  <th class="mdl-data-table__cell">Баланс</th>
  <th class="mdl-data-table__cell--non-numeric">Валюта</th>
  <th class="mdl-data-table__cell">Час створення</th>
  </tr>
  </thead>
  <tbody>';

  $accs_search_results = Accounts::findAccounts($_GET['accname'], $_GET['accnum'], $_GET['acccurr']);

  foreach($accs_search_results as $r) {
    $content .= '<tr><td class="mdl-data-table__cell--non-numeric">'.$r['name'].'</td>
    <td class="mdl-data-table__cell">'.$r['number'].'</td>
    <td class="mdl-data-table__cell">'.$r['balance'].'</td>
    <td class="mdl-data-table__cell--non-numeric">'.Currency::getCurrencyRow($r['currency_id'])['code'].'</td>
    <td class="mdl-data-table__cell">'.$r['create_time'].'</td></tr>';
  }

  $content .= '</tbody></table>';
}

$page->setContent($content);
$page->create();
 ?>
