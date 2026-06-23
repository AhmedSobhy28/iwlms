/// <reference types="vite/client" />

import type { DefineComponent } from 'vue';

declare module '*.vue' {
    const component: DefineComponent<object, object, unknown>;
    export default component;
}

declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}
