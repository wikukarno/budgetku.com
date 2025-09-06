import { argon2id } from 'hash-wasm';

const wrapper = {
  ArgonType: { Argon2id: 'Argon2id' },
  async hash(opts) {
    const password = opts.pass;
    const salt = opts.salt;
    const memorySize = opts.mem || 65536; // KiB
    const iterations = opts.time || 3;
    const parallelism = opts.parallelism || 1;
    const hashLength = opts.hashLen || 32;
    const out = await argon2id({ password, salt, memorySize, iterations, parallelism, hashLength, outputType: 'binary' });
    return { hash: out };
  },
};

if (typeof window !== 'undefined') {
  window.argon2 = wrapper;
}

export default wrapper;
