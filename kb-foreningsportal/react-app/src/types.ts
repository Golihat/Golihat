import React from 'react';

export type PageConfig = {
  id: string;
  title: string;
  element: React.ReactNode;
};

export type StatDatum = {
  label: string;
  value: string | number;
  delta?: string;
};

export type TableColumn<T> = {
  header: string;
  accessor: keyof T;
};

export type TableRow = Record<string, string | number>;
