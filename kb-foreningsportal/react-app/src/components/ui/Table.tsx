import React from 'react';
import { TableColumn, TableRow } from '../../types';

type TableProps<T extends TableRow> = {
  columns: TableColumn<T>[];
  data: T[];
};

const Table = <T extends TableRow>({ columns, data }: TableProps<T>) => (
  <table className="kb-table">
    <thead>
      <tr>
        {columns.map((col) => (
          <th key={String(col.accessor)}>{col.header}</th>
        ))}
      </tr>
    </thead>
    <tbody>
      {data.map((row, idx) => (
        <tr key={idx}>
          {columns.map((col) => (
            <td key={String(col.accessor)}>{String(row[col.accessor])}</td>
          ))}
        </tr>
      ))}
    </tbody>
  </table>
);

export default Table;
