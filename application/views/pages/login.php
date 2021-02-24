<br/>
<br/>
<div class="container">
    <p class="h1">Login</p>
    <form action="<?= base_url('login/acessar') ?>" method="POST" >
        <div class="form-row">
            <div class="form-group col-sm-12">
                <label for="exampleInputEmail1" class="form-label">Email</label>
                <input type="email" class="form-control <?= form_error('email') ? 'is-invalid' : ''; ?>" id="exampleInputEmail1" name="email" value="<?= set_value('email'); ?>">
                <?= form_error('email'); ?>
            </div>
            <div class="form-group col-sm-12">
                <label for="exampleInputPassword1" class="form-label">Senha</label>
                <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : ''; ?>" id="exampleInputPassword1" aria-describedby="passwordHelp" name="password">
                <?= form_error('password') ?>
                <div id="passwordHelp" class="form-text">Não compartilhe seu acesso com ninguém.</div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</div>