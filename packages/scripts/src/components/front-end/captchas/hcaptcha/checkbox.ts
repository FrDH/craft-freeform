import events from '@lib/plugin/constants/event-types';
import { addListeners } from '@lib/plugin/helpers/event-handling';
import type { FreeformEvent } from 'types/events';

import type { hCaptchaConfig, Size, Theme, Version } from './utils/script-loader';
import { loadHCaptcha } from './utils/script-loader';

const form: HTMLFormElement = document.querySelector('form[data-id="{{ formAnchor }}"]') as HTMLFormElement;
const config: hCaptchaConfig = {
  sitekey: '{{ siteKey }}',
  theme: '{{ theme }}' as Theme,
  size: '{{ size }}' as Size,
  lazyLoad: Boolean('{{ lazyLoad }}'),
  version: '{{ version }}' as Version,
  locale: '{{ locale }}',
} as const;

let captchaId: string;

const createCaptcha = (event: FreeformEvent): HTMLDivElement | null => {
  const existingElement = form.querySelector<HTMLDivElement>('.h-captcha');
  if (existingElement) {
    return existingElement;
  }

  const { sitekey, theme, size } = config;

  const captchaElement = document.createElement('div');
  captchaElement.classList.add('h-captcha');

  const targetElement = event.form.querySelector('[data-freeform-hcaptcha-container]');
  if (!targetElement) {
    return null;
  }

  targetElement.appendChild(captchaElement);

  captchaId = hcaptcha.render(captchaElement, {
    sitekey,
    theme,
    size,
  });

  return captchaElement;
};

form.addEventListener(events.form.ready, (event: FreeformEvent) => {
  loadHCaptcha(event.form, config).then(() => {
    createCaptcha(event);
  });
});

addListeners(form, [events.form.ajaxAfterSubmit, events.form.afterFailedSubmit], async (event: FreeformEvent) => {
  await loadHCaptcha(event.form, { ...config, lazyLoad: false });

  const captchaElement = createCaptcha(event);
  if (captchaElement) {
    hcaptcha.reset(captchaId);
  }
});
