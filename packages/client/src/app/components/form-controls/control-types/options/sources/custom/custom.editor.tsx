import React from 'react';
import { HelpText } from '@components/elements/help-text';
import Bool from '@components/form-controls/control-types/bool/bool';
import {
  Button,
  Cell,
  DeleteIcon,
  Input,
  Row,
  TableContainer,
  TabularOptions,
} from '@components/form-controls/control-types/table/table.editor.styles';
import { useCellNavigation } from '@components/form-controls/hooks/use-cell-navigation';
import { PreviewEditor } from '@components/form-controls/preview/previewable-component.styles';
import { useOnKeypress } from '@ff-client/hooks/use-on-keypress';
import { PropertyType } from '@ff-client/types/properties';
import translate from '@ff-client/utils/translations';

import type { CustomOptions } from '../../options.types';

import {
  addOption,
  deleteOption,
  updateChecked,
  updateOption,
} from './custom.operations';

type Props = {
  value: CustomOptions;
  updateValue: (value: CustomOptions) => void;
};

export const CustomEditor: React.FC<Props> = ({ value, updateValue }) => {
  const { options = [], useCustomValues = false } = value;

  const { activeCell, setActiveCell, setCellRef } = useCellNavigation(
    options.length,
    useCustomValues ? 2 : 1
  );

  const addCell = (): void => {
    setActiveCell(options.length, 0);
    updateValue(addOption(value));
  };

  useOnKeypress(
    {
      callback: (event: KeyboardEvent): void => {
        if (event.key === 'Enter') {
          addCell();
        }
      },
    },
    [value]
  );

  return (
    <PreviewEditor>
      <Bool
        property={{
          label: 'Use custom values',
          handle: 'useCustomValues',
          type: PropertyType.Boolean,
        }}
        value={useCustomValues}
        updateValue={() =>
          updateValue({
            ...value,
            useCustomValues: !useCustomValues,
          })
        }
      />
      {!!options.length && (
        <TableContainer>
          <TabularOptions>
            <tbody>
              {options.map((option, index) => (
                <Row key={index}>
                  <Cell>
                    <Input
                      type="text"
                      value={option.label}
                      placeholder={translate('Label')}
                      autoFocus={activeCell === `${index}:0`}
                      ref={(element) => setCellRef(element, index, 0)}
                      onFocus={() => setActiveCell(index, 0)}
                      onChange={(event) =>
                        updateValue(
                          updateOption(
                            index,
                            {
                              ...option,
                              label: event.target.value,
                            },
                            value
                          )
                        )
                      }
                    />
                  </Cell>

                  {useCustomValues && (
                    <Cell>
                      <Input
                        type="text"
                        value={option.value}
                        placeholder={translate('Value')}
                        autoFocus={activeCell === `${index}:1`}
                        ref={(element) => setCellRef(element, index, 1)}
                        onFocus={() => setActiveCell(index, 1)}
                        onChange={(event) =>
                          updateValue(
                            updateOption(
                              index,
                              {
                                ...option,
                                value: event.target.value,
                              },
                              value
                            )
                          )
                        }
                      />
                    </Cell>
                  )}

                  {options.length > 1 && (
                    <>
                      <Cell tiny>
                        <Bool
                          property={{
                            label: '',
                            handle: `${index}-check`,
                            type: PropertyType.Boolean,
                          }}
                          value={option.checked}
                          updateValue={() =>
                            updateValue(
                              updateChecked(
                                index,
                                {
                                  ...option,
                                  checked: !option.checked,
                                },
                                value
                              )
                            )
                          }
                        />
                      </Cell>

                      <Cell tiny>
                        <Button
                          onClick={() => {
                            updateValue(deleteOption(index, value));
                            setActiveCell(Math.max(index - 1, 0), 0);
                          }}
                        >
                          <DeleteIcon />
                        </Button>
                      </Cell>
                    </>
                  )}
                </Row>
              ))}
            </tbody>
          </TabularOptions>
        </TableContainer>
      )}

      <HelpText>
        <span
          dangerouslySetInnerHTML={{
            __html: translate(
              'Press <b>enter</b> while editing a cell to add a new row.'
            ),
          }}
        />
      </HelpText>
    </PreviewEditor>
  );
};