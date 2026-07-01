/**
 * Modelos de mensagens com SweetAlert2 - uso em todo o sistema
 * Depende de: SweetAlert2 (incluir no HTML antes deste script)
 */

const Mensagens = {
  /**
   * Exibe mensagem de erro no login conforme código na URL (?erro=)
   * Remove o parâmetro da URL após exibir
   */
  erroLogin(codigo) {
    const mensagens = {
      '1': {
        icon: 'error',
        title: 'Dados incorretos',
        text: 'E-mail ou senha inválidos. Verifique e tente novamente.',
      },
      inativo: {
        icon: 'warning',
        title: 'Usuário inativo',
        text: 'Sua conta está inativa. Entre em contato com o administrador.',
      },
      bloqueado: {
        icon: 'error',
        title: 'Acesso temporariamente bloqueado',
        text: 'Muitas tentativas de login. Aguarde alguns minutos e tente novamente.',
      },
      sessao: {
        icon: 'info',
        title: 'Sessão encerrada',
        text: 'Faça login novamente para continuar.',
      },
    };

    const msg = mensagens[codigo];
    if (!msg) return;

    return Swal.fire({
      icon: msg.icon,
      title: msg.title,
      text: msg.text,
      confirmButtonText: 'OK',
      confirmButtonColor: '#667eea',
    }).then(() => {
      if (typeof history !== 'undefined' && history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.delete('erro');
        history.replaceState({}, document.title, url.pathname + url.search);
      }
    });
  },

  /**
   * Sucesso genérico
   */
  sucesso(title = 'Sucesso!', text = 'Operação realizada com sucesso.') {
    return Swal.fire({
      icon: 'success',
      title,
      text,
      confirmButtonText: 'OK',
      confirmButtonColor: '#667eea',
    });
  },

  /**
   * Erro genérico
   */
  erro(title = 'Erro', text = 'Ocorreu um erro. Tente novamente.') {
    return Swal.fire({
      icon: 'error',
      title,
      text,
      confirmButtonText: 'OK',
      confirmButtonColor: '#667eea',
    });
  },

  /**
   * Aviso genérico
   */
  aviso(title = 'Atenção', text = '') {
    return Swal.fire({
      icon: 'warning',
      title,
      text,
      confirmButtonText: 'OK',
      confirmButtonColor: '#667eea',
    });
  },

  /**
   * Confirmação (ex: antes de excluir)
   * Retorna Promise que resolve true se confirmar, false se cancelar
   */
  confirmar(opcoes = {}) {
    const padrao = {
      title: 'Confirmar',
      text: 'Deseja realmente continuar?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sim',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#667eea',
      cancelButtonColor: '#6c757d',
    };
    return Swal.fire({ ...padrao, ...opcoes }).then((result) => result.isConfirmed);
  },

  /**
   * Toast rápido (canto da tela)
   */
  toast(icon, title) {
    return Swal.fire({
      toast: true,
      position: 'top-end',
      icon,
      title,
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
    });
  },

  /**
   * Inicializa Flatpickr em todos os elementos que tiverem data-flatpickr
   * Ex.: <input type="text" data-flatpickr data-flatpickr-mode="single">
   * Opções em data-flatpickr-opções (JSON) ou segundo argumento
   */
  iniciarFlatpickr(opcoesPadrao = {}) {
    if (typeof flatpickr === 'undefined') return;
    const el = document.querySelectorAll('[data-flatpickr]');
    const locale = typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.pt ? 'pt' : undefined;
    el.forEach((input) => {
      let opcoes = { ...opcoesPadrao, locale: opcoesPadrao.locale || locale };
      const dataOpcoes = input.getAttribute('data-flatpickr-opcoes');
      if (dataOpcoes) {
        try {
          opcoes = { ...opcoes, ...JSON.parse(dataOpcoes) };
        } catch (e) {}
      }
      flatpickr(input, opcoes);
    });
  },
};

// Na página de login: verificar mensagem de sessão (exposta em window.LOGIN_MENSAGEM) e exibir
document.addEventListener('DOMContentLoaded', function () {
  const codigo = typeof window !== 'undefined' ? window.LOGIN_MENSAGEM : null;
  if (codigo) {
    Mensagens.erroLogin(codigo);
  }
});
