import type { Layout, Page } from '@editor/builder/types/layout';
import type { RootState } from '@editor/store';

import type { Field } from '../fields';
import { pageSelecors } from '../pages/pages.selectors';
import { rowSelectors } from '../rows/rows.selectors';

export const layoutSelectors = {
  one:
    (uid: string) =>
    (state: RootState): Layout | undefined =>
      state.layout.layouts.find((layout) => layout.uid === uid),
  currentPageLayout: (state: RootState): Layout =>
    layoutSelectors.pageLayout(pageSelecors.current(state))(state),
  pageLayout:
    (page: Page) =>
    (state: RootState): Layout | undefined =>
      state.layout.layouts.find((layout) => layout.uid === page?.layoutUid),
  cartographed: {
    pageFieldList: (state: RootState) => {
      const pages = pageSelecors.all(state);

      const cartograph: Array<{ page: string; fields: Field[] }> = [];

      pages.forEach((page) => {
        const layout = layoutSelectors.pageLayout(page)(state);
        const rows = rowSelectors.inLayout(layout)(state);

        const fields: Field[] = [];
        rows.forEach((row) => {
          state.layout.fields
            .filter((field) => field.rowUid === row.uid)
            .forEach((field) => {
              fields.push(field);
            });
        });

        cartograph.push({ page: page.uid, fields });
      });

      return cartograph;
    },
    fullLayoutList: (state: RootState) => {
      const pages = pageSelecors.all(state);

      const cartograph: Array<Array<Field[]>> = [];

      pages.forEach((page) => {
        const layout = layoutSelectors.pageLayout(page)(state);
        const rows = rowSelectors.inLayout(layout)(state);

        const rowList: Array<Field[]> = [];
        rows.forEach((row) => {
          const fields: Field[] = [];
          state.layout.fields
            .filter((field) => field.rowUid === row.uid)
            .forEach((field) => {
              fields.push(field);
            });

          rowList.push(fields);
        });

        cartograph.push(rowList);
      });

      return cartograph;
    },
  },
} as const;