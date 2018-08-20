<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Žinutės</title>
    <link rel="stylesheet" media="screen" type="text/css" href="/css/screen.css" />
</head>
<body>
<div id="wrapper">
    <h1>Jūsų žinutės</h1>
    <form id="message-form" method="post" action="<?php echo \App\Core\Route::getInstance()->url('storeMessage'); ?>">
        <p class="<?php if($this->hasError('fullname')) { echo 'err'; } ?>">
            <label for="fullname">Vardas, pavardė *</label><br/>
            <input id="fullname" type="text" name="fullname" value="" />
            <?php if($this->hasError('fullname')) { $this->showErrors('fullname'); } ?>
        </p>
        <p class="<?php if($this->hasError('birthdate')) { echo 'err'; } ?>">
            <label for="birthdate">Gimimo data *</label><br/>
            <input id="birthdate" type="text" name="birthdate" value="" placeholder="yyyy-mm-dd"/>
            <?php if($this->hasError('birthdate')) { $this->showErrors('birthdate'); } ?>
        </p>
        <p class="<?php if($this->hasError('email')) { echo 'err'; } ?>">
            <label for="email">El.pašto adresas</label><br/>
            <input id="email" type="text" name="email" value="" />
            <?php if($this->hasError('email')) { $this->showErrors('email'); } ?>
        </p>
        <p class="<?php if($this->hasError('message')) { echo 'err'; } ?>">
            <label for="message">Jūsų žinutė *</label><br/>
            <textarea id="message"></textarea>
            <?php if($this->hasError('message')) { $this->showErrors('message'); } ?>
        </p>
        <p>
            <span>* - privalomi laukai</span>
            <input id="submit-button" type="submit" value="Skelbti" />
            <img id="loader" src="../public/img/ajax-loader.gif" alt="" />
        </p>
    </form>
    <ul id="messagesContainer">
        <?php if(count($messages) < 1) :?>
            <li>
                <strong>Šiuo metu žinučių nėra. Būk pirmas!</strong>
            </li>
        <?php else: ?>
            <?php foreach($messages as $message) {?>
                <li>
                    <span><?php echo $this->_($message->createdAt); ?></span>
                    <?php if($this->_($message->email)):?>
                    <a href="mailto:<?php echo $this->_($message->email); ?>"><?php echo $this->_($message->firstName) . " " . $this->_($message->lastName);?></a>
                    <?php else: ?>
                    <?php echo $this->_($message->firstName) . " " . $this->_($message->lastName);?>
                    <?php endif; ?>
                    , <?php echo $this->_($message->age); ?> m.<br/>
                    <?php echo $this->_($message->message); ?>
                </li>
            <?php } ?>
        <?php endif; ?>
    </ul>
    <?php if ($pages > 1) :?>
    <p id="pages">
        <?php if(isset($_GET['page']) && $_GET['page'] > 1) : ?>
            <a href="?page=<?php echo (int) $_GET['page'] - 1; ?>" title="atgal">atgal</a>
        <?php endif; ?>

        <?php for($i = 1; $i <= $pages; $i++) {?>
            <?php if(!isset($_GET['page']) && $i == 1):?>
                <?php echo $i; ?>
            <?php elseif(isset($_GET['page']) && $_GET['page'] == $i) :?>
                <?php echo $i ?>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>" title="<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php } ?>

        <?php if($pages > 1) : ?>
            <?php if(isset($_GET['page'])) :?>
                <?php if($_GET['page'] < $pages) :?>
                    <a href="?page=<?php echo (int) $_GET['page'] + 1; ?>" title="toliau">toliau</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="?page=2" title="toliau">toliau</a>
            <?php endif; ?>
        <?php endif; ?>
    </p>
    <?php endif; ?>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    var MESSAGE_FORM_POST = "<?php echo \App\Core\Route::getInstance()->url('storeMessage'); ?>";
</script>
<script src="/js/script.js" type="application/javascript"></script>
</html>
