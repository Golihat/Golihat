import React from 'react';
import Card from '../../components/ui/Card';
import Button from '../../components/ui/Button';
import Modal from '../../components/ui/Modal';

const AdminSettingsPage: React.FC = () => {
  const [open, setOpen] = React.useState(false);

  return (
    <div className="kb-grid">
      <Card title="Feature toggles" actions={<Button onClick={() => setOpen(true)}>Visa JSON</Button>}>
        <p>Hantera globala inställningar och moduler i WordPress-admin.</p>
      </Card>
      <Modal title="Aktiva toggles" open={open} onClose={() => setOpen(false)}>
        <pre>{JSON.stringify({ features: 'Laddas från WP options' }, null, 2)}</pre>
      </Modal>
    </div>
  );
};

export default AdminSettingsPage;
