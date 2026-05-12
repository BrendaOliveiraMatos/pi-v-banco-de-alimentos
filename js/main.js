// 1. Confirmação antes de excluir um produto
function confirmarExclusao(idProduto) {
    // Exibe um modal nativo do navegador perguntando se o usuário tem certeza
    let confirmacao = confirm("Tem certeza que deseja excluir este produto do estoque?");
    
    // Se ele clicar em "OK" (true), redirecionamos para o script PHP que faz a exclusão
    if (confirmacao) {
        window.location.href = "excluir_produto.php?id=" + idProduto;
    }
}

// 2. Validação simples para não deixar cadastrar validade no passado
document.addEventListener("DOMContentLoaded", function() {
    const inputValidade = document.getElementById("data_validade");
    
    if (inputValidade) {
        // CORREÇÃO 1: Mudamos de "change" para "blur" (só valida quando você sai do campo)
        inputValidade.addEventListener("blur", function() {
            
            // Se o campo estiver vazio, a gente encerra a função aqui e não faz nada
            if (!this.value) return;

            let dataDigitada = new Date(this.value);
            let hoje = new Date();
            
            // CORREÇÃO 2: Só dispara o alerta se o ano já estiver preenchido de forma lógica (ex: maior que 2000)
            // Isso evita que o navegador avalie anos incompletos como "0020"
            if (dataDigitada.getFullYear() < 2000) return;
            
            // Zera as horas para comparar apenas os dias
            hoje.setHours(0,0,0,0);
            
            // Corrige a diferença de fuso horário
            dataDigitada.setMinutes(dataDigitada.getMinutes() + dataDigitada.getTimezoneOffset());

            if (dataDigitada < hoje) {
                alert("Atenção: Você está cadastrando um produto que já está vencido!");
            }
        });
    }
});