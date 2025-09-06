import * as openpgp from 'openpgp';

if (typeof window !== 'undefined') {
  window.openpgp = openpgp;
}

export default openpgp;

