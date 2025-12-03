import React from 'react';
import Table from '../../components/ui/Table';
import { TableRow } from '../../types';

const AdminOrgsPage: React.FC = () => {
  const data: TableRow[] = [
    { name: 'IF Blå', campaign: 'Vår 2024', boxes: 320 },
    { name: 'BK Röd', campaign: 'Sommar 2024', boxes: 210 },
  ];

  return (
    <div>
      <h2>Föreningar</h2>
      <Table
        columns={[
          { header: 'Namn', accessor: 'name' },
          { header: 'Kampanj', accessor: 'campaign' },
          { header: 'Sålda boxar', accessor: 'boxes' },
        ]}
        data={data}
      />
    </div>
  );
};

export default AdminOrgsPage;
