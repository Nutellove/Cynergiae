<?php $view->extend('JavascriptClassBundle::layout.html.php') ?>

<p>It Works!</p>

<?php $pathToWeb = "/cynergiae_git/web/"; ?>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/vendor/mootools/mootools-core-1.3.1-full-nocompat.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/vendor/mootools/mootools-more-1.3.1.1.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/javascript/mootools/BaseEntityAbstract.class.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/javascript/mootools/BaseAnt.class.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/javascript/mootools/Ant.class.js" type="text/javascript"></script>

<script type="text/javascript">
var ant;
var box;

window.addEvent('domready', function(){
  $('result_box').set('html', '<em>I am the result box</em>');

  ant = new Ant({controllerBaseUrl: 'cynergiae_git/web/jsclass'});

  box = function(s){
    $('result_box').set('html', $('result_box').get('html')+'<br />'+s);
  };

  $('doGeorges').addEvent('click', function(e){
    ant.georges();
  });

  $('getName').addEvent('click', function(e){
    box(ant.getName());
  });

  $('setHungry').addEvent('click', function(e){
    box(ant.setIsHungry(true));
  });

  $('doLoad').addEvent('click', function(e){
    ant.load(1);
  });

  $('doSave').addEvent('click', function(e){
    ant.save();
  });

});
</script>
<p>
  <input id="doGeorges" type="button" value="Georges ?" />
  <input id="doLoad" type="button" value="Load" />
  <input id="getName" type="button" value="getName" />
  <input id="setHungry" type="button" value="setHungry" />
  <input id="doSave" type="button" value="Save" />
</p>

<div id="result_box"></div>
