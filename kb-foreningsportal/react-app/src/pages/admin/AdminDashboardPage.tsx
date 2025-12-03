import React from 'react';
import StatsCard from '../../components/stats/StatsCard';
import SalesChart from '../../components/charts/SalesChart';
import DiagnosticsPanel from '../../components/diagnostics/DiagnosticsPanel';

const AdminDashboardPage: React.FC = () => {
  return (
    <div className="kb-grid">
      <StatsCard
        title="Snabbstatistik"
        stats={[
          { label: 'Sålda boxar', value: 1200, delta: '+12% mot föregående' },
          { label: 'Aktiva kampanjer', value: 14 },
          { label: 'Provision att betala', value: '42 000 kr' },
        ]}
      />
      <SalesChart />
      <DiagnosticsPanel
        issues={[
          { id: 'orders', message: '3 osynkade ordrar', suggestion: 'Klicka för att koppla ordrar' },
          { id: 'qr', message: 'En QR-kod saknas', suggestion: 'Regenerera QR' },
        ]}
      />
    </div>
  );
};

export default AdminDashboardPage;
