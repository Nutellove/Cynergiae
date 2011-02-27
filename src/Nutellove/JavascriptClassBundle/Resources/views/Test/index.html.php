<?php $view->extend('JavascriptClassBundle::layout.html.php') ?>

It Works!

<?php $pathToWeb = "/cynergiae_git/web/"; ?>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/vendor/mootools/mootools-core-1.3.1-full-nocompat.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/vendor/mootools/mootools-more-1.3.1.1.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/javascript/mootools/BaseEntityAbstract.class.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/javascript/mootools/BaseAnt.class.js" type="text/javascript"></script>
<script src="<?php echo $pathToWeb ?>bundles/javascriptclass/javascript/mootools/Ant.class.js" type="text/javascript"></script>

<script type="text/javascript">
window.addEvent('domready', function(){
  $('result_box').set('html', '<em>I am the result box</em>');

  ant = new Ant('JavascriptClassBundle', 'Ant', {});


});
</script>

<div id="result_box"></div>
