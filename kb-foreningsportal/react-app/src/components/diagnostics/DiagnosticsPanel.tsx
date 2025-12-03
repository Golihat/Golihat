import React from 'react';
import Card from '../ui/Card';
import Button from '../ui/Button';

type DiagnosticsPanelProps = {
  issues: { id: string; message: string; suggestion?: string }[];
};

const DiagnosticsPanel: React.FC<DiagnosticsPanelProps> = ({ issues }) => (
  <Card title="Diagnostik">
    {issues.length === 0 && <p>Inga kända problem.</p>}
    <ul className="kb-list">
      {issues.map((issue) => (
        <li key={issue.id} className="kb-list__item">
          <div>
            <strong>{issue.message}</strong>
            {issue.suggestion && <p className="kb-list__hint">{issue.suggestion}</p>}
          </div>
          <Button variant="secondary">Åtgärda</Button>
        </li>
      ))}
    </ul>
  </Card>
);

export default DiagnosticsPanel;
