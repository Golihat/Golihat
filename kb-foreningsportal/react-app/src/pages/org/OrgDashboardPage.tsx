import React from 'react';
import StatsCard from '../../components/stats/StatsCard';
import SalesChart from '../../components/charts/SalesChart';
import DiagnosticsPanel from '../../components/diagnostics/DiagnosticsPanel';

const OrgDashboardPage: React.FC = () => (
  <div className="kb-grid">
    <StatsCard
      title="Ert läge"
      stats={[
        { label: 'Sålda boxar', value: 85, delta: '+5 mot igår' },
        { label: 'Provision', value: '3 400 kr' },
        { label: 'Medlemmar som sålt', value: 22 },
      ]}
    />
    <SalesChart />
    <DiagnosticsPanel
      issues={[
        { id: 'material', message: 'Material ej hämtat', suggestion: 'Ladda ner affischer' },
      ]}
    />
  </div>
);

export default OrgDashboardPage;
