import { getCaptchaContainer, loadCaptchaScript, readCaptchaConfig } from '../../common.script-loader';

const scriptUrl = 'https://js.hcaptcha.com/1/api.js?render=explicit';
const TYPE = 'hcaptcha';

export enum Version {
  CHECKBOX = 'checkbox',
  INVISIBLE = 'invisible',
}

export const loadHCaptcha = (form: HTMLFormElement, forceLoad?: boolean): Promise<void> => {
  const container = getContainer(form);
  if (!container) {
    return Promise.resolve();
  }

  const url = new URL(scriptUrl);

  const { locale } = readConfig(container);
  if (locale) {
    url.searchParams.append('hl', locale);
  }

  return loadCaptchaScript(url, TYPE, form, forceLoad);
};

export const getContainer = (form: HTMLFormElement) => getCaptchaContainer(TYPE, form);
export const readConfig = (container: HTMLElement) => readCaptchaConfig<Version>(container);
