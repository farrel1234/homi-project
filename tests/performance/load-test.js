import http from 'k6/http';
import { sleep, check } from 'k6';

export const options = {
  vus: 5,
  duration: '30s',
  thresholds: {
    http_req_duration: ['p(95)<2000'], // 95% request harus di bawah 2 detik
  },
};

const BASE_URL = 'http://127.0.0.1:8000';

export default function () {
  // 1. Uji endpoint public /api/ping
  let resPing = http.get(`${BASE_URL}/api/ping`);
  check(resPing, {
    'ping status is 200': (r) => r.status === 200,
    'ping response has ok': (r) => r.json().status === 'ok',
  });
  sleep(1);

  // 2. Uji endpoint public list tenants
  let resTenants = http.get(`${BASE_URL}/api/tenants`);
  check(resTenants, {
    'tenants status is 200': (r) => r.status === 200,
  });
  sleep(1);

  // 3. Uji endpoint portal utama (welcome page)
  let resPortal = http.get(`${BASE_URL}/`);
  check(resPortal, {
    'portal status is 200': (r) => r.status === 200,
  });
  sleep(1);
}
