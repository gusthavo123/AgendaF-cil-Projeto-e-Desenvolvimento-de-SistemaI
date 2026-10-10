<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Entrar</h1>

                <form method="post" action="login.php" novalidate>
                    <?= csrf_campo() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= e($email) ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>

                <p class="text-muted small mt-3 mb-0">
                    Usuário de teste: admin@agendafacil.com / admin123
                </p>
            </div>
        </div>
    </div>
</div>
