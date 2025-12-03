import React from 'react';
import Table from '../../components/ui/Table';
import Button from '../../components/ui/Button';
import { TableRow } from '../../types';

const OrgMembersPage: React.FC = () => {
  const data: TableRow[] = [
    { name: 'Lisa', sales: 12, contact: 'lisa@example.com' },
    { name: 'Omar', sales: 8, contact: 'omar@example.com' },
  ];

  return (
    <div>
      <div className="kb-toolbar">
        <h2>Medlemmar</h2>
        <Button variant="primary">Importera CSV</Button>
      </div>
      <Table
        columns={[
          { header: 'Namn', accessor: 'name' },
          { header: 'Sålda boxar', accessor: 'sales' },
          { header: 'Kontakt', accessor: 'contact' },
        ]}
        data={data}
      />
    </div>
  );
};

export default OrgMembersPage;
