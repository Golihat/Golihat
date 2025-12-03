import React from 'react';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';

const AdminReportsPage: React.FC = () => (
  <div className="kb-grid">
    <Card title="Exportera rapporter" actions={<Button variant="primary">Exportera CSV</Button>}>
      <p>Generera aggregerad försäljning, provision och kampanjutfall.</p>
    </Card>
    <Card title="Revision">
      <p>Skapa underlag för revisorer eller externa granskare.</p>
      <Button variant="secondary">Skapa revisionspaket</Button>
    </Card>
  </div>
);

export default AdminReportsPage;
