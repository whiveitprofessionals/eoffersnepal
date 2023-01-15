<div class="container pt50 pb50">

<div class="row">

    <div class="col-md-12 text-center title">
        <h2><?php te( 'theme_suggest_title', 'Suggest a Store/Brand' ); ?></h2>
    </div>

</div>

<div class="row pt50">

    <div class="col-md-8 offset-md-2">
        <?php echo suggest_store_form( array('intent' => ( me() ? 1 : 2 ) ) ); ?>
    </div>

</div>

</div>