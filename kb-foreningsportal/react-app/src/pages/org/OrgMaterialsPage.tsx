import React from 'react';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';

const OrgMaterialsPage: React.FC = () => (
  <div className="kb-grid">
    <Card title="Affischer">
      <Button variant="secondary">Ladda ner</Button>
    </Card>
    <Card title="Flyers">
      <Button variant="secondary">Ladda ner</Button>
    </Card>
    <Card title="SoMe-kit">
      <Button variant="secondary">Visa inlägg</Button>
    </Card>
  </div>
);

export default OrgMaterialsPage;
