function confirmarExclusao(produtoId) {
    Swal.fire({
        title: "Você tem certeza?",
        text: "Essa ação não poderá ser desfeita!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sim, excluir!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `excluir_produto.php?id=${produtoId}`;
        }
    });
}
