import React from 'react';
import Table from '../../components/ui/Table';
import { TableRow } from '../../types';

const AdminAdminsPage: React.FC = () => {
  const data: TableRow[] = [
    { name: 'Anna Admin', commission: '10 kr/box', orgs: 4 },
    { name: 'Björn Bas', commission: '5% av provision', orgs: 2 },
  ];

  return (
    <div>
      <h2>Admins</h2>
      <Table
        columns={[
          { header: 'Namn', accessor: 'name' },
          { header: 'Provision', accessor: 'commission' },
          { header: 'Föreningar', accessor: 'orgs' },
        ]}
        data={data}
      />
    </div>
  );
};

export default AdminAdminsPage;
